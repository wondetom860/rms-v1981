<?php

namespace frontend\controllers;

use Yii;
use backend\models\SemesterRegistrationControlac;
use backend\models\SemesterRegistrationControlacSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * SemesterRegistrationControldController implements the CRUD actions for SemesterRegistrationControld model.
 */
class SemesterRegistrationControlacController extends Controller
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
                       'actions' => [],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all SemesterRegistrationControld models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new SemesterRegistrationControlacSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new SemesterRegistrationControlac();

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
            'model'=>$model,
        ]);
    }

    /**
     * Displays a single SemesterRegistrationControld model.
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
     * Creates a new SemesterRegistrationControld model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new SemesterRegistrationControlac();

       if ($model->load(Yii::$app->request->post())) {

            $model->src_created_by = $model->src_updated_by = Yii::$app->user->identity->id;
            $model->src_created_at =  $model->src_updated_at = date('Y-m-d h:i:s');

            // $model->src_acadYear_relId=1;

            // echo "<pre>";
            // var_dump($model);
            // die();
            if ($model->save()) {
               return $this->redirect(['index']);
            }else{
                var_dump($model->errors);die();
            }
            // 
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing SemesterRegistrationControld model.
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
     * Deletes an existing SemesterRegistrationControld model.
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
     * Finds the SemesterRegistrationControld model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return SemesterRegistrationControld the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = SemesterRegistrationControlac::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
