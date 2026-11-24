<?php

namespace frontend\modules\CourseAssessment\controllers;

use Yii;
use frontend\modules\CourseAssessment\models\InstanceMarkAssignment;
use frontend\modules\CourseAssessment\models\InstanceMarkAssignmentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * InstanceMarkAssignmentController implements the CRUD actions for InstanceMarkAssignment model.
 */
class InstanceMarkAssignmentController extends Controller
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
                ],
            ],
        ];
    }

    /**
     * Lists all InstanceMarkAssignment models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new InstanceMarkAssignmentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single InstanceMarkAssignment model.
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
     * Creates a new InstanceMarkAssignment model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($categoryId = null)
    {
        $model = new InstanceMarkAssignment();
        $model->ima_catagorySetup_relId = $categoryId;
        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format =  \yii\web\Response::FORMAT_JSON;
            if($model->ima_possiblePoint > $model->imaCatagorySetupRel->getRemainingWeight()){
                return 'The possible point is greater than the remaining point';
            }
            if($model->ima_instanceName == null){
                $model->ima_instanceName = $model->getInstanceNameRecom();
            }
            $model->ima_created_by = Yii::$app->user->id;
            $model->ima_created_at = date('Y-m-d H:i:s');
            if ($model->save()) {
                return 1;
            } else {
                return $model->errors;
            }
        }
        $model->ima_instanceName = $model->ima_instanceName ?? $model->getInstanceNameRecom();
        return $this->renderAjax('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing InstanceMarkAssignment model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            Yii::$app->response->format =  \yii\web\Response::FORMAT_JSON;
            if($model->ima_approved == 1){
                return 'This mark assignment is already approved. You can NOT edit it.';
            }
            if($model->ima_possiblePoint > ($model->old_ima_possiblePoint + $model->imaCatagorySetupRel->getRemainingWeight())){
                return 'The possible point can NOT be more than the previous value: '. $model->old_ima_possiblePoint;
            }
            if($model->ima_instanceName == null){
                $model->ima_instanceName = $model->getInstanceNameRecom();
            }
            $model->ima_updated_by = Yii::$app->user->id;
            $model->ima_updated_at = date('Y-m-d H:i:s');
            if ($model->save()) {
                return 1;
            } else {
                return $model->errors;
            }
        }

        $model->old_ima_possiblePoint = $model->ima_possiblePoint;
        return $this->renderAjax('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing InstanceMarkAssignment model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        Yii::$app->response->format =  \yii\web\Response::FORMAT_JSON;
        return $this->findModel($id)->delete();

        // return $this->redirect(['index']);
    }

    /**
     * Finds the InstanceMarkAssignment model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return InstanceMarkAssignment the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = InstanceMarkAssignment::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
