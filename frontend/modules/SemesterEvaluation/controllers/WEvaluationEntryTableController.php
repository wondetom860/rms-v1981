<?php

namespace frontend\modules\SemesterEvaluation\controllers;

use Yii;
use backend\modules\SemesterEvaluation\models\WEvaluationEntryTable;
use backend\modules\SemesterEvaluation\models\WEvaluationEntryTableSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WEvaluationEntryTableController implements the CRUD actions for WEvaluationEntryTable model.
 */
class WEvaluationEntryTableController extends Controller
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
     * Lists all WEvaluationEntryTable models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new WEvaluationEntryTableSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single WEvaluationEntryTable model.
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
     * This is the model class for table "w_evaluation_entry_table".
     *
     * @property integer $id
     * @property integer $eet_et_relId
     * @property string $eet_evaluator_role
     * @property string $eet_reviewee_role
     * @property double $eet_max_point
     * @property integer $eet_created_by
     * @property string $eet_created_at
     * @property string $eet_updated_at
     * @property integer $eet_updated_by
     * @property string $data
     *
     * @property User $eetCreatedBy
     * @property WEvaluationTypes $eetEtRel
     * @property User $eetUpdatedBy
     * @property WEvaluationPointEntryTable[] $wEvaluationPointEntryTables
     * @property WSemesterEvaluation[] $wSemesterEvaluations
     */

    /**
     * Creates a new WEvaluationEntryTable model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WEvaluationEntryTable();

        if ($model->load(Yii::$app->request->post())) {


            $model->eet_created_at = date('Y-m-d h:i:s');
            $model->eet_created_by = Yii::$app->user->identity->id;
            $model->data = $model->getData($model);

            if ($model->save()) {
                return $this->redirect(['index']);
            }else{
                var_dump($model->getErrors());die();
            }
            
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing WEvaluationEntryTable model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->data = $model->getData($model);
            $model->eet_updated_by = Yii::$app->user->identity->id;
            $model->eet_updated_at = date('Y-m-d H:i:s');
            if ($model->save()) {
                return $this->redirect(['index']);
            }else{
                var_dump($model->getErrors());die();
            }
            
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing WEvaluationEntryTable model.
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
     * Finds the WEvaluationEntryTable model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WEvaluationEntryTable the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WEvaluationEntryTable::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
