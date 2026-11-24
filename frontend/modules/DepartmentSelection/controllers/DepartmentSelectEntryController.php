<?php

namespace frontend\modules\DepartmentSelection\controllers;

use Yii;
use frontend\modules\DepartmentSelection\models\DepartmentSelectEntry;
use frontend\modules\DepartmentSelection\models\SelectionSettingEntry;
use frontend\modules\DepartmentSelection\models\DepartmentSelectEntrySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DepartmentSelectEntryController implements the CRUD actions for DepartmentSelectEntry model.
 */
class DepartmentSelectEntryController extends Controller
{
    /**
     * @inheritdoc
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
                       'actions' => ['index', 'update', 'delete','view'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
                   [
                       'actions' => ['approve-selection','approve-selection-action','get-entries','cancel-selection-action','educ-background-info'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Registrar',],
                   ],[
                       'actions' => ['get-selection-form','save-selection','create','index','view','educ-background-info','delete'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Student',],
                   ],
               ]
           ]
        ];
    }

    public function actionCancelSelectionAction(){
        $entryId = isset($_POST['entryId']) && !empty($_POST['entryId']) ? intval($_POST['entryId']) : die('INVALID_RQUEST');
        $model = DepartmentSelectEntry::findOne($entryId);

        if (!is_null($model->approved_dept_relId)) {
            $transaction = Yii::$app->db->beginTransaction();
            $error = "";

            $modelStudent = $model->studentRel;

            if (is_null($modelStudent->std_group_realId)) {
                return "Previous Student Group / Section is UKNOWN... Please Try another Option";
            }
            $modelStudent->std_dept_relId = $modelStudent->stdGroupReal->gs_dept_relId;
            // $modelStudent->std_dept_relId = $dept_relId;
            $modelStudent->std_updated_at = date("Y-m-d h:i:s");
            $modelStudent->std_updated_by = Yii::$app->user->identity->id;

            if ($modelStudent->update()) {
                $model->approved_dept_relId = NULL;
                if ($model->update()) {
                    $transaction->commit();
                    return 1;
                }else{
                    $error .= "Error: ".json_encode($model->getErrors());
                }
            }else{
                $error .= "Error: ".json_encode($modelStudent->getErrors());
            }
        }else{
            return "__INVALID_OPERATION";
        }
    }

    public function actionEducBackgroundInfo(){
        $entryId = isset($_POST['entryId']) && !empty($_POST['entryId']) ? intval($_POST['entryId']) : die('INVALID_RQUEST');
        $model = DepartmentSelectEntry::findOne($entryId);

        if ($model && count($model) > 0) {
            return $this->renderPartial('_includes/educ-background-info',['model' => $model->studentRel->getEducationRecord(),'modelStudent' => $model->studentRel]);
        }
    }
    public function actionApproveSelectionAction(){
        $approvedChoice = isset($_POST['approvedChoice']) && !empty($_POST['approvedChoice']) ? (string)$_POST['approvedChoice'] : die('INVALID_RQUEST');

        $approvedChoice = json_decode($approvedChoice);
        $total = 0;

        if ($approvedChoice && count($approvedChoice) > 0) {
            $total = count($approvedChoice);
            $scount = 0;
            $error  = "";
            $transaction = Yii::$app->db->beginTransaction();
            foreach ($approvedChoice as $entry_id => $dept_relId) {
                $model = DepartmentSelectEntry::findOne($entry_id);

                $model->approved_dept_relId = $dept_relId;
                $model->approved_at = date('Y-m-d h:i:s');
                
                $model->data = $model->getData();

                if ($model->update()) {
                    $modelStudent = $model->studentRel;

                    $modelStudent->std_dept_relId = $dept_relId;
                    $modelStudent->std_updated_at = date("Y-m-d h:i:s");
                    $modelStudent->std_updated_by = Yii::$app->user->identity->id;

                    if ($modelStudent->update()) {
                        $scount++;
                    }else{
                        $error .= "Error - ".json_encode($modelStudent->getErrors());
                    }
                }else{
                    $error .= "Error - ".json_encode($model->getErrors());
                }
            }

            if ($scount == $total || empty($error)) {
                $transaction->commit();
                return 1;
            }else{
                $transaction->rollBack();
                return $error;
            }
        }
    }

    public function actionGetEntries(){//approve-selection
        $settingId = isset($_POST['settingId']) && !empty($_POST['settingId']) ? intval($_POST['settingId']) : die('INVALID_RQUEST');
        $model = DepartmentSelectEntry::find()->where([
            'setting_relId' => $settingId,
        ])->all();
        return $this->renderPartial('_includes/dept_choice',['model' => $model]);
    }

    public function actionApproveSelection(){//approve-selection
        $model = DepartmentSelectEntry::find()->groupBy('setting_relId')->orderBy(['created_at' => SORT_DESC])->all();
        return $this->render('approve-selection',['selections' => $model]);
    }

    public function actionSaveSelection(){
        $selectedDeptList = isset($_POST['selectedDepts']) && !empty($_POST['selectedDepts']) ? (string)$_POST['selectedDepts'] : die('INValiD REqUESt');


    }

    public function actionGetSelectionForm(){
        $settingId = isset($_POST['settingId']) && !empty($_POST['settingId']) ? intval($_POST['settingId']) : die('INVALID_REQUEST');
        // $model = new DepartmentSelectEntry();

        $settingEntry = SelectionSettingEntry::findOne($settingId);

        if ($settingEntry && count($settingEntry) && \Yii::$app->user->can('Student')) {
            $student = \backend\models\Student::findOne(['std_user_relId' => \Yii::$app->user->identity->id]);

            $selectionEntry = $settingEntry->checkIfRequested();
            if ($selectionEntry && count($selectionEntry) > 0) {
                return "Selection already made, Please click <a target='_blank' href='/DepartmentSelection/department-select-entry/view?id={$selectionEntry->id}' /> here </a> to see detail.";
            }else if (!$settingEntry->isScheduleActive()) {
                return "Schedule is not active for this action.";
            }

            $model = new DepartmentSelectEntry();
            $model->setting_relId = $settingId;
            $model->student_relId = $student->id;

            return $this->renderPartial('selection-form',['model' => $model]);

        }
    }


    /**
     * Lists all DepartmentSelectEntry models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DepartmentSelectEntrySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DepartmentSelectEntry model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        if (Yii::$app->user->can('Student')) {
            $student = \backend\models\Student::findOne(['std_user_relId' => \Yii::$app->user->identity->id]);

            $model = $this->findModel($id);

            if ($model && $model->student_relId == $student->id) {
                return $this->render('view', [
                    'model' => $this->findModel($id),
                ]);
            }

            throw new \yii\web\ForbiddenHttpException("Action Not allowed", 1);
            

        }
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new DepartmentSelectEntry model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DepartmentSelectEntry();

        if ($model->load(Yii::$app->request->post())) {

            $model->created_at = date("Y-m-d h:i:s");
            $model->created_by = Yii::$app->user->identity->id;

            $model->data = $model->getData();

            if ($model->save()) {
                return $this->redirect(['index']);
            }

            var_dump($model->getErrors());die();
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DepartmentSelectEntry model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if (!is_null($model->approved_dept_relId)) {
            throw new \yii\web\ForbiddenException("Cannot change approved department selection request.", 1);
            
        }

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DepartmentSelectEntry model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if (is_null($model->approved_dept_relId)) {
            if (Yii::$app->user->can('Student') && !$model->settingRel->isScheduleActive()) {
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message','You can NOT perform this action at this time.');
            }else{
                $model->delete();
            }            
        }else{
            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','You can NOT delete the approved selection entry!');
        }        
        return $this->redirect(['../select-department/']);
    }

    /**
     * Finds the DepartmentSelectEntry model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DepartmentSelectEntry the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DepartmentSelectEntry::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('decris', 'The requested page does not exist.'));
    }
}
