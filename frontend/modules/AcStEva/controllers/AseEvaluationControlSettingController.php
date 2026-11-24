<?php

namespace frontend\modules\AcStEva\controllers;

use Yii;
use frontend\modules\AcStEva\models\AseEvaluationControlSetting;
use backend\models\FacultyCourseAssignment;
use frontend\modules\AcStEva\models\AseDepartmentEvaluationSchedule;
use frontend\modules\AcStEva\models\AseEvaluationControlSettingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AseEvaluationControlSettingController implements the CRUD actions for AseEvaluationControlSetting model.
 */
class AseEvaluationControlSettingController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                    'create2' => ['POST'],
                ],
            ],
            'access' => [
               'class' => \yii\filters\AccessControl::class,
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => \yii\filters\AccessRule::class,
               ],
               'rules' => [
                   [
                       'actions' => [],
                       'allow' => true,
                       'roles' => ['Admin','Registrar'],
                   ],[
                       'actions' => ['index','view','generate-control-entries','get-evaluators-for-candidate','create2','show-evaluators','get-candidates-for-evaluator'],
                       'allow' => true,
                       'roles' => ['DepartmentHead'],
                   ],
                   [
                       'actions' => ['index','get-candidates-for-evaluator'],
                       'allow' => true,
                       'roles' => ['AcademicDean','Instructor','Student','Technical Assistant'],
                   ]
               ]
           ]
        ];
    }

    public function actionGetCandidatesForEvaluator($deptPlanId){
        $uid = Yii::$app->user->id;
        $deptPlanModel = AseDepartmentEvaluationSchedule::findOne($deptPlanId);
        // ->andWhere([' IN ',$uid,' JSON_ARRAY(evaluators) '])
        $model = AseEvaluationControlSetting::find()
            ->where([
                'dept_plan_relId' => $deptPlanId,
                'finalized' => 0
            ])
            ->all();
        return $this->renderPartial('__report/candidates-list-inthe-plan',['candidates' => $model,'deptPlanModel' => $deptPlanModel]);

    }

    /**
     * Lists all AseEvaluationControlSetting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AseEvaluationControlSettingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionShowEvaluators($id){
        $model = $this->findModel($id);
        return $this->renderPartial('__report/genr16/evalautors',['model' => $model]);
    }

    public function actionCreate2(){
        $deptPlanId = intval($_POST['deptPlanId']);
        $candidate_staff_relId = intval($_POST['candidate_staff_relId']);
        $evalautors = $_POST['evalautors'];
        $facultyModelId = $_POST['facultyModelId'];
        // $bundleId = $_POST['bundleId'];

        $facultyModel = FacultyCourseAssignment::findOne($facultyModelId);
        $deptPlanModel = AseDepartmentEvaluationSchedule::findOne($deptPlanId);

        if ($facultyModel->instRel->inst_user_relId != $candidate_staff_relId) {
            return "Data provided is NOT valid, Please review it.";            
        }

        $model = new AseEvaluationControlSetting();
        $model->candidate_staff_relId = $candidate_staff_relId;
        $model->dept_plan_relId = $deptPlanModel->id;
        $model->course_relId = $facultyModel->coff2detRel->coff2det_course_relId;
        $model->evaluated_as = $facultyModel->assign_as;
        $model->evaluators = $evalautors;
        $model->faculty_assignment_relId = $facultyModel->id;

        $ex = $model->checkIfExists();
        if ($ex && count($ex) > 0) {
            $ex->evaluators = $model->evaluators;
            $ex->faculty_assignment_relId = $ex->faculty_assignment_relId ? $ex->faculty_assignment_relId : $facultyModel->id;
            $ex->created_at = time();
            $ex->created_by = Yii::$app->user->id;
            $ex->getData();

            if ($ex->save()) {
                Yii::$app->session->setFlash('type','info');
                Yii::$app->session->setFlash('message','Existing record has been updated, Please review it.');
                return 1;
            }
            return json_encode($ex->getErrors());
        }
        $model->created_at = time();
        $model->created_by = Yii::$app->user->id;
        $model->getData();

        if ($model->validate() && $model->save()) {
            Yii::$app->session->setFlash('type','success');
            Yii::$app->session->setFlash('message','New record saved, Please review it.');
            return 1;
        }

        return json_encode($model->getErrors());
    }

    public function actionGetEvaluatorsForCandidate($facuId,$deptPlanId){
        $model = new AseEvaluationControlSetting();
        $deptPlanModel = AseDepartmentEvaluationSchedule::findOne($deptPlanId);
        $candidates = $model->getEvaluators($deptPlanId,$deptPlanModel,$facuId);

        if ($candidates[2] == 1) {//Inst/TA case
            return $this->renderPartial('__report/staff-evaluators-inst-ta',['candidates' => $candidates[0],'facultyModel' => $candidates[1],'deptPlanModel' => $deptPlanModel]);
        }else if($candidates[2] == 2){//Student case
            return $this->renderPartial('__report/staff-evaluators-students',['candidates' => $candidates[0],'facultyModel' => $candidates[1],'deptPlanModel' => $deptPlanModel]);
        }else if($candidates[2] == 3){//Academic Dean case
            return $this->renderPartial('__report/staff-evaluators-user',['candidates' => $candidates[0],'facultyModel' => $candidates[1],'deptPlanModel' => $deptPlanModel]);
        }else if($candidates[2] == 4){//HoD case
            return $this->renderPartial('__report/staff-evaluators-user',['candidates' => $candidates[0],'facultyModel' => $candidates[1],'deptPlanModel' => $deptPlanModel]);
        }
         
    }

    public function actionGenerateControlEntries($deptPlanId){
        $model = new AseEvaluationControlSetting();
        $deptPlanModel = AseDepartmentEvaluationSchedule::findOne($deptPlanId);
        $candidates = $model->generateControlEntries($deptPlanId,$deptPlanModel);
        // $this->layout = "@frontend/views/layouts/printLayout";
        // let it redirect to evaluation-controll-setting/view
        if ($candidates === false) {
            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','No candidates found for the plan, Please check the plan and try again.');
        }else{
            Yii::$app->session->setFlash('type','success');
            Yii::$app->session->setFlash('message','Control entries generated successfully.');
        }
        return $this->redirect(['/AcStEva/ase-semester-evaluation/view','id' => $deptPlanModel->header_relId]);
        // return $this->renderPartial('__report/candidates-list-inthe-plan',['candidates' => $candidates,'deptPlanModel' => $deptPlanModel,'model' => $model]);
        // return "Running candidate staff categorizing algorithm, this may take a while for coding and running. take a deep breat, have cup of tea and and visit me later:, <code>WondeTom</code>";
    }

    /**
     * Displays a single AseEvaluationControlSetting model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new AseEvaluationControlSetting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AseEvaluationControlSetting();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AseEvaluationControlSetting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AseEvaluationControlSetting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the AseEvaluationControlSetting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AseEvaluationControlSetting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AseEvaluationControlSetting::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
