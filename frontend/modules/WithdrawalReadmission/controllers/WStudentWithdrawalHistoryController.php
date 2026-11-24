<?php

namespace frontend\modules\WithdrawalReadmission\controllers;

use Yii;
use frontend\modules\WithdrawalReadmission\models\WStudentWithdrawalHistory;
use frontend\modules\WithdrawalReadmission\models\WStudentWithdrawalHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WStudentWithdrawalHistoryController implements the CRUD actions for WStudentWithdrawalHistory model.
 */
class WStudentWithdrawalHistoryController extends Controller
{
    /**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::className(),
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],
            'access' => [
               'class' => \yii\filters\AccessControl::className(),
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => \yii\filters\AccessRule::className(),
               ],
               'rules' => [
                   [
                       'actions' => ['index', 'view','list-removed-courses','get-student-semester-status'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
                   [
                       'actions' => ['index', 'delete', 'list-removed-courses','get-student-semester-status','approve-last','delete'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Registrar',],
                   ],[
                       'actions' => ['create','delete'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['DepartmentHead',],
                   ],[
                       'actions' => ['index','view','list-removed-courses','get-student-semester-status'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Registrar','AcademicDean','DepartmentHead'],
                   ]
               ]
           ]
        ];
    }

    /**
     * Lists all WStudentWithdrawalHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WStudentWithdrawalHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionApproveLast($wthdrwlId){
        $model = $this->findModel($wthdrwlId);
        if ($model->swh_HOD_approved) {
            $model->swh_lastApproved = 1;
            $model->swh_lastApproved_at = date("d-m-Y");
            $model->swh_lastApproved_by = Yii::$app->user->id;
            $model->update(false);
        }
    }

    public function actionListRemovedCourses($wthdrwlId){
        $model = $this->findModel($wthdrwlId);

        return $this->renderPartial('__report/removed-course-list',['model' => $model]);
    }

    public function actionGetStudentSemesterStatus($wthdrwlId){//
        $model = $this->findModel($wthdrwlId);
        $gas = $model->sectionRel;

        $srd2s = \backend\models\SemesterRegistration2Detail::find()
            ->innerJoin('semester_registration_2','semester_registration_2.id=semester_registration_2_detail.srd2_semreg_relId')
            ->where([
                'semreg_acadYear_relId' => $gas->gasGsetRel->gs_acadYear_relId,
                'semreg_semester_relId' => $gas->gasGsetRel->gs_sem_relId,
                'semreg_clslvl_relId' => $gas->gasGsetRel->gs_clslvl_relId,
                'semreg_std_relId' => $gas->gas_std_relId,
            ])
            ->all();
        if ($srd2s && count($srd2s) > 0) {
            return $this->renderPartial('@frontend/views/semester-registration/__report/semreg-details',['srd2s' => $srd2s,'model' => $srd2s[0]->srd2SemregRel]);
        }

        return "Record not available.";

        
    }

    /**
     * Displays a single WStudentWithdrawalHistory model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->user->can('DepartmentHead')) {
            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
            if ($model->swhStdRel->std_dept_relId != $inst->inst_dep_relId) {
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message','Invalid request: Student is NOT from your department.');
                return $this->redirect('index');
            }
        }
        return $this->render('view', [
            'model' => $model,
        ]);
    }

    /**
     * Creates a new WStudentWithdrawalHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($semregId)
    {
        $model = new WStudentWithdrawalHistory();
        $modelSemreg = \backend\models\SemesterRegistration2::findOne($semregId);

        $model->swh_std_relId = $modelSemreg->semreg_std_relId;
        $model->swh_acadYear_relId = $modelSemreg->semreg_acadYear_relId;
        $model->swh_sem_relId = $modelSemreg->semreg_semester_relId;
        $exsts = $model->checkIfRequestedBefore();
        if (!is_null($exsts)) {
            Yii::$app->session->setFlash('type','warning');
            Yii::$app->session->setFlash('message','Request Already Made before, Pleaase review it.');
           return $this->redirect(['view', 'id' => $exsts->id]); 
        }

        // store request details:
        if ($model->load(Yii::$app->request->post())) {
            // check if withdrawal is possible with selected type of reason:
            $possible = $model->checkIfRequestPossible($modelSemreg);
            if (!$possible) {
                return $this->render('create', [
                    'model' => $model,
                    'modelSemreg' => $modelSemreg,
                ]);
            }

            // hookup semreg_entries__
            // 
            $tr = Yii::$app->db->beginTransaction();

            $model->swh_created_at = date("Y-m-d H:i:s");
            $model->swh_created_by = Yii::$app->user->id;
            $model->swh_lastApproved = NULL;
            $model->swh_course_removed = $model->swhReasonRel->remove_course_enrolls;
            $model->swh_stdSigned = 0;

            if ($model->save()) {
                $model->hookUpSrd2s($modelSemreg);
                $tr->commit();
                return $this->redirect(['view', 'id' => $model->id]);
            }
            $tr->rollBack();
            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','Error: '.json_encode($model->getErrors()));
            
        }

        return $this->render('create', [
            'model' => $model,
            'modelSemreg' => $modelSemreg,
        ]);
    }
    /**
     * Updates an existing WStudentWithdrawalHistory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (Yii::$app->user->can('DepartmentHead')) {
            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => \Yii::$app->user->id]);
            if ($model->swhStdRel->std_dept_relId != $inst->inst_dep_relId || $model->swh_lastApproved || $model->swh_approved) {
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message','Error: You can not update registrar approved request.');
                return $this->redirect(['view','id' => $model->id]);
            }
        }
        if ($model->load(Yii::$app->request->post())) {

            $model->swh_updated_at = date("Y-m-d H:i:s");
            $model->swh_updated_by=Yii::$app->user->id;
            $model->swh_stdSigned = 0;
            $model->swh_stdSigned_at = null;
            $model->swh_stdSigned_by = null;
            if ($model->save()) {
                Yii::$app->session->setFlash('type','success');
                Yii::$app->session->setFlash('message','Request updated successfully. Student should approve/accept the request, again.');
            }else{
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message','Error: Record updating failed.'.json_encode($model));
            }

            return $this->redirect(['view', 'id' => $model->id]);
            
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing WStudentWithdrawalHistory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->user->can('DepartmentHead')) {
            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => \Yii::$app->user->id]);
            if ($model->swhStdRel->std_dept_relId != $inst->inst_dep_relId || $model->swh_lastApproved || $model->swh_approved) {
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message','Error: You can not remove registrar approved request.');
                return $this->redirect(['view','id' => $model->id]);
            }
        }else if($model->swh_lastApproved){
            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','Error: You can not remove approved request.');
            return $this->redirect(['view','id' => $model->id]);
        }

        $model->delete();
        return $this->redirect(['index']);
    }

    /**
     * Finds the WStudentWithdrawalHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WStudentWithdrawalHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WStudentWithdrawalHistory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
