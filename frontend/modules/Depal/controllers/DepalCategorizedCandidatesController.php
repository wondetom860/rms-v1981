<?php

namespace frontend\modules\Depal\controllers;

use Yii;
use frontend\modules\Depal\models\DepalCategorizedCandidates;
use frontend\modules\Depal\models\DepalSetting;
use frontend\modules\Depal\models\DepalCategorizedCandidatesSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DepalCategorizedCandidatesController implements the CRUD actions for DepalCategorizedCandidates model.
 */
class DepalCategorizedCandidatesController extends Controller
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
            'access' => [
               'class' => \yii\filters\AccessControl::class,
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => \yii\filters\AccessRule::class,
               ],
               'rules' => [
                   [
                       'actions' => ['index', 'update', 'delete','view','get-grouped-students','show-grouped-students','get-allotting-report'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
                   [
                       'actions' => ['index','finalize-selection','get-grouped-students','view','get-allotting-report','show-grouped-students','get-allotting-report'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Registrar','AcademicDean','DepartmentHead'],
                   ]
               ]
           ]
        ];
    }

    public function actionFinalizeSelection(){
        $modelId = isset($_POST['modelId']) && !empty($_POST['modelId']) ? intval($_POST['modelId']) : die('Invalid request');
        $model = $this->findModel($modelId);
        return $model->finalizeSelection();
    }

    public function actionGetAllottingReport(){
        $categorizedid = isset($_GET['categorizedid']) && !empty($_GET['categorizedid']) ? intval($_GET['categorizedid']) : die('Invalid request');

        $model = $this->findModel($categorizedid);
        $this->layout = "@frontend/views/layouts/printLayout";
        return $this->render('__report/allotment-report',['model' => $model]);
    }

    public function actionShowGroupedStudents(){
        $settingId = isset($_GET['settingid']) && !empty($_GET['settingid']) ? intval($_GET['settingid']) : die('Invalid request');
        $settingModel = DepalSetting::findOne($settingId);
        $this->layout = "@frontend/views/layouts/printLayout";
        return $this->render('__report/grouped-candidates-report2',['settingModel' => $settingModel]);
    }

    public function actionGetGroupedStudents(){
        $settingId = isset($_GET['settingid']) && !empty($_GET['settingid']) ? intval($_GET['settingid']) : die('Invalid request');
        $model = new DepalCategorizedCandidates();
        $model->groupEligibleCandidaets($settingId);
    }

    /**
     * Lists all DepalCategorizedCandidates models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DepalCategorizedCandidatesSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DepalCategorizedCandidates model.
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
     * Creates a new DepalCategorizedCandidates model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DepalCategorizedCandidates();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DepalCategorizedCandidates model.
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
     * Deletes an existing DepalCategorizedCandidates model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DepalCategorizedCandidates model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DepalCategorizedCandidates the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DepalCategorizedCandidates::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
