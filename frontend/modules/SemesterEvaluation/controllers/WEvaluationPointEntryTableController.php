<?php

namespace frontend\modules\SemesterEvaluation\controllers;

use Yii;
use backend\modules\SemesterEvaluation\models\WEvaluationPointEntryTable;
use backend\modules\SemesterEvaluation\models\WEvaluationPointEntryTableSearch;
use \backend\modules\SemesterEvaluation\models\WEvaluationPoints;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WEvaluationPointEntryTableController implements the CRUD actions for WEvaluationPointEntryTable model.
 */
class WEvaluationPointEntryTableController extends Controller
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
     * Lists all WEvaluationPointEntryTable models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WEvaluationPointEntryTableSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WEvaluationPointEntryTable model.
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
     * Creates a new WEvaluationPointEntryTable model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WEvaluationPointEntryTable();
        $modelEP = WEvaluationPoints::find()->all();
        if ($model->load(Yii::$app->request->post())) {

            $model->data = $model->getData($model);
            if ($model->save()) {
                return $this->redirect(['index']);
            }else{
                var_dump($model->getErrors());
            }
        } else {
            return $this->render('create', [
                'model' => $model,
                'modelEP'=>$modelEP,
            ]);
        }
    }

    /**
     * Updates an existing WEvaluationPointEntryTable model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->data = $model->getData($model);
            if ($model->save()) {
                return $this->redirect(['index']);
            }else{
                var_dump($model->getErrors());
            }
            
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing WEvaluationPointEntryTable model.
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
     * Finds the WEvaluationPointEntryTable model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WEvaluationPointEntryTable the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WEvaluationPointEntryTable::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
