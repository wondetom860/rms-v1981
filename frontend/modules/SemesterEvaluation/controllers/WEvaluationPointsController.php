<?php

namespace frontend\modules\SemesterEvaluation\controllers;

use Yii;
use backend\modules\SemesterEvaluation\models\WEvaluationPoints;
use backend\modules\SemesterEvaluation\models\WEvaluationPointsSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WEvaluationPointsController implements the CRUD actions for WEvaluationPoints model.
 */
class WEvaluationPointsController extends Controller
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
     * Lists all WEvaluationPoints models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WEvaluationPointsSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WEvaluationPoints model.
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
     * Creates a new WEvaluationPoints model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WEvaluationPoints();

        if ($model->load(Yii::$app->request->post())) {

            $model->ep_ceated_at = date('Y-m-d H:i:s');
            $model->ep_created_by = Yii::$app->user->identity->id;
            $model->data = $model->getData($model);
            if ($model->save()) {
                return $this->redirect(['index']);
            }else{
                var_dump($model->getErrors());
                die();
            }
            // 
            
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing WEvaluationPoints model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $model->ep_updated_at = date('Y-m-d H:i:s');
            $model->ep_updated_by = Yii::$app->user->identity->id;
            
            $model->data = $model->getData($model);
            if ($model->save()) {
                return $this->redirect(['index']);
            }
            
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing WEvaluationPoints model.
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
     * Finds the WEvaluationPoints model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WEvaluationPoints the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WEvaluationPoints::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
