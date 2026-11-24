<?php

namespace frontend\controllers;

use Yii;
use backend\models\ContAssessCatagory;
use backend\models\ContAssessCatagorySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/*
* @property integer $id
 * @property integer $cat_course_relId
 * @property string $cat_name
 * @property double $cat_maxPoint
 * @property integer $cat_created_by
 * @property string $cat_created_at
 * @property string $cat_updated_at
 * @property integer $cat_updated_by
 *
 * @property User $catCreatedBy
 * @property User $catUpdatedBy
 * @property Course $catCourseRel
 * @property ContAssessMarkAssignment[] $contAssessMarkAssignments
 */
/**
 * ContAssessCatagoryController implements the CRUD actions for ContAssessCatagory model.
 */
class ContAssessCatagoryController extends Controller
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
        ];
    }

    /**
     * Lists all ContAssessCatagory models.
     * @return mixed
     */
    public function actionIndex($model = NULL)
    {
        $searchModel = new ContAssessCatagorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        
        if (is_null($model)) {
            $model = new ContAssessCatagory();
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('index_Ajax', [
                        'searchModel' => $searchModel,
                        'dataProvider' => $dataProvider,
                        'model'=>$model,
            ]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model'=>$model,
        ]);
    }

    /**
     * Displays a single ContAssessCatagory model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new ContAssessCatagory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ContAssessCatagory();

        if ($model->load(Yii::$app->request->post())) {

            // Model created_at.

            $model->cat_created_by = $model->cat_updated_by = Yii::$app->user->identity->id;
            $model->cat_created_at = $model->cat_updated_at = date('Y-m-d h:i:s');

            // check for dupplicate info.
            $exists = \backend\models\ContAssessCatagory::findOne(['cat_course_relId'=>$model->cat_course_relId,'cat_name_relId'=>$model->cat_name_relId]);

            if ($exists && count($exists) > 0) {
                Yii::$app->session->setFlash('message', "Such record alredy registered, If you need additional distribution, try mark assignment.");
            }else{
                // check if the total Mark assigned is not morethan 100.
                if ($model->cat_maxPoint > 100) {
                    Yii::$app->session->setFlash('message', "Max point for this catagory could NOT be more than 100.");
                }else{
                    // check if the total mark is lessthan the remaining total mark out of 100.
                    $remPoint = $model->remainingPoint($model->cat_course_relId);
                    if($model->cat_maxPoint > $remPoint) {
                        Yii::$app->session->setFlash('message', "Max point for this catagory could NOT be more than {$remPoint}.");
                    }else{
                        if ($model->save()) {
                            return $this->redirect(['index']);
                        }else{
                            Yii::$app->session->setFlash('message', "Setup ssaving failed, please re-try.");
                        }
                    }
                }
            }
        return $this->redirect(['index',['model'=>$model]]);
        } else {
           return $this->redirect(['index']);
        }
    }

    /**
     * Updates an existing ContAssessCatagory model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['index']);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ContAssessCatagory model.
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
     * Finds the ContAssessCatagory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ContAssessCatagory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ContAssessCatagory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}