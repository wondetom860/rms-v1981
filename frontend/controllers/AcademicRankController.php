<?php

namespace frontend\controllers;

use Yii;
use backend\models\AcademicRank;
use backend\models\AcademicRankSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AcademicRankController implements the CRUD actions for AcademicRank model.
 */
class AcademicRankController extends Controller
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
                       'actions' => ['index','create', 'update', 'delete'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all AcademicRank models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AcademicRankSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
           $model = new AcademicRank();
        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
             'model'=>$model,
        ]);
    }

    /**
     * Displays a single AcademicRank model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    // Returns a list of academic ranks

    public function actionRanks(){
        $acrs = AcademicRank::find()->all();
        if (!empty($acrs)) {
            $count = 0;
            foreach ($acrs as $acr) {
                echo ++$count.". ". $acr->acr_name.'<br>';
            }
        }else{
            echo "No Academic Ranks registered yet.";
        }
    }
    /**
     * Creates a new AcademicRank model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AcademicRank();

        if ($model->load(Yii::$app->request->post())) {

            $model->acr_created_at = $model->acr_updated_at = date('Y-m-d h:i:s');
            $model->acr_created_by = $model->acr_updated_by = Yii::$app->user->identity->id;

            if ($model->save()) {
                return $this->redirect(['index']);
            }else{
                print_r($model->errors);die();
            }
            // 
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing AcademicRank model.
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
     * Deletes an existing AcademicRank model.
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
     * Finds the AcademicRank model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AcademicRank the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AcademicRank::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
