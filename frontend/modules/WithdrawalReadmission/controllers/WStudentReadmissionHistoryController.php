<?php

namespace frontend\modules\WithdrawalReadmission\controllers;

use Yii;
use frontend\modules\WithdrawalReadmission\models\WStudentReadmissionHistory;
use frontend\modules\WithdrawalReadmission\models\WStudentReadmissionHistorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WStudentReadmissionHistoryController implements the CRUD actions for WStudentReadmissionHistory model.
 */
class WStudentReadmissionHistoryController extends Controller
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
                       'actions' => ['index', 'delete','view','get-active-scetions',],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
                   [
                       'actions' => ['index','view'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Registrar','AcademicDean','DepartmentHead'],
                   ]
               ]
           ]
        ];
    }

    public function actionGetActiveScetions($id){
        $model = $this->findModel($id);
        return $this->renderPartial('__report/sections-per-academic-year',['model' =>$model]);
    }

    /**
     * Lists all WStudentReadmissionHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WStudentReadmissionHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WStudentReadmissionHistory model.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionView($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->user->can('DepartmentHead')) {
            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
            if ($model->srhStdRel->std_dept_relId != $inst->inst_dep_relId) {
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
     * Creates a new WStudentReadmissionHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WStudentReadmissionHistory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing WStudentReadmissionHistory model.
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
     * Deletes an existing WStudentReadmissionHistory model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->srh_approved || $model->srh_HOD_approved) {
            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','Invalid Operation: You can not remove approved readmission request.');
            return $this->redirect(['view','id' => $id]);
        }else{
            $model->delete();
        }
        return $this->redirect(['index']);
    }

    /**
     * Finds the WStudentReadmissionHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WStudentReadmissionHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WStudentReadmissionHistory::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
