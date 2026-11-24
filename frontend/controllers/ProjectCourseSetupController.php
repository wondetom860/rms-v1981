<?php

namespace frontend\controllers;

use Yii;
use backend\models\ProjectCourseSetup;
use backend\models\ProjectCourseSetupSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \common\components\CommonControllerFunctions as CCF;

/**
 * ProjectCourseSetupController implements the CRUD actions for ProjectCourseSetup model.
 */
class ProjectCourseSetupController extends Controller
{
    public $acadYearId;
    public $semId;
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
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
        ];
    }

    /**
     * Lists all ProjectCourseSetup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ProjectCourseSetupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new ProjectCourseSetup();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model'=>$model,
        ]);
    }

    /**
     * Displays a single ProjectCourseSetup model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    // free-project-groups
    public function actionFreeProjectGroups(){
        $gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId'])?intval($_POST['gsetId']):die();
        $model = new ProjectCourseSetup();
        return $model->getfreeProjectGroups($gsetId);
    }
    /**
     * Creates a new ProjectCourseSetup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ProjectCourseSetup();

        if ($model->load(Yii::$app->request->post())) {

            $this->__constructor();
            $model->pcs_acadYear_relId = $this->acadYearId;
            $model->pcs_sem_relId = $this->semId;
            $model->pcs_created_by = $model->pcs_updated_by = Yii::$app->user->identity->id;
            $model->pcs_created_at = $model->pcs_updated_at = date('Y-m-d h:i:s');

            if ($model->save()) {
                return $this->redirect(['index']);   
            }else{
                print_r($model->errors);die();
            }
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ProjectCourseSetup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ProjectCourseSetup model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ProjectCourseSetup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ProjectCourseSetup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ProjectCourseSetup::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
