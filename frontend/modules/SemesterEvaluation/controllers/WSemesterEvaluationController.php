<?php

namespace frontend\modules\SemesterEvaluation\controllers;

use Yii;
use backend\modules\SemesterEvaluation\models\WSemesterEvaluation;
use backend\modules\SemesterEvaluation\models\WEvaluationEntryTable;
use backend\modules\SemesterEvaluation\models\WSemesterEvaluationSearch;
use yii\web\Controller;
use \common\components\checkActionCalendar;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * WSemesterEvaluationController implements the CRUD actions for WSemesterEvaluation model.
 */
class WSemesterEvaluationController extends Controller
{

    const ACTION_NAME = "Semester evaluation";
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
     * Lists all WSemesterEvaluation models.
     * @return mixed
     */
    public function actionIndexOld()
    {
        $searchModel = new WSemesterEvaluationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Lists all WSemesterEvaluation models.
     * @return mixed
     */
    public function actionIndex(){
        // 1.check if the calendar is open for 'semesterEvaluation'

        $calCheckObj = new checkActionCalendar();
        $evalConfigEntryObj = new WEvaluationEntryTable();

        if ($calCheckObj->actionCalendarOpen(self::ACTION_NAME)) {
            // 2. Load evaluation configuration for current user
            $evalEntry = $evalConfigEntryObj->getEvaluationConfs();
            // 3.loop over evaluation configurations
        }else{
            throw new NotFoundHttpException("Error Processing Request", 1);
            
        }
    }

    /**
     * Displays a single WSemesterEvaluation model.
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
     * Creates a new WSemesterEvaluation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new WSemesterEvaluation();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing WSemesterEvaluation model.
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
     * Deletes an existing WSemesterEvaluation model.
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
     * Finds the WSemesterEvaluation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return WSemesterEvaluation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = WSemesterEvaluation::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
