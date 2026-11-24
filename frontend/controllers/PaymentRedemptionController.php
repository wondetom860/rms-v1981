<?php

namespace frontend\controllers;

use Yii;
use backend\models\PaymentRedemption;
use backend\models\PaymentRedemptionSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use backend\models\Student;

/**
 * PaymentRedemptionController implements the CRUD actions for PaymentRedemption model.
 */
class PaymentRedemptionController extends Controller
{
    /**
     * {@inheritdoc}
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
                       'actions' => ['index','create', 'update', 'delete','get-student','get-prev-requests','view'],
                       'allow' => true,
                       'roles' => ['Admin',],
                   ],
                    [
                       'actions' => ['index','change-status','view','create','get-student','update'],
                       'allow' => true,
                       'roles' => ['Registrar'],
                   ],
               ]
           ]
        ];
    }

    public function actionChangeStatus(){
        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die('Invalid request');

        $model = $this->findModel($id);

        if ($model->used != 1) {//it is either rejected or NOT approved Yet.
            if ($model->approved_by != null) {
                $model->approved_by = NULL;
                $model->approved_at = null;
            }else{
                $model->approved_by = Yii::$app->user->identity->id;
                $model->approved_at = time();
            }

            $model->data = $model->getData();

            if ($model->update()) {
                return 1;
            }else{
                return json_encode($model->getErrors());
            }
        }
    }
    public function actionGetStudent(){
        $student_search_key = isset($_POST['student_search_key']) && !empty($_POST['student_search_key']) ? $_POST['student_search_key'] : die('Invalid request');

        return $this->renderPartial('__report/student-search-result',['model' => Student::find()->where(['like','std_regId',$student_search_key])->all()]);
    }

    public function actionGetPrevRequests(){
        $stdId = isset($_POST['stdId']) && !empty($_POST['stdId']) ? intval($_POST['stdId']) : die('InvalId request');

        return $this->renderPartial('__report/prev-requests',['models'=> PaymentRedemption::find()->where(['std_relId' => $stdId])->all()]);
    }

    /**
     * Lists all PaymentRedemption models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new PaymentRedemptionSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single PaymentRedemption model.
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
     * Creates a new PaymentRedemption model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new PaymentRedemption();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->exists()) {
                Yii::$app->session->setFlash('type','error');  
                Yii::$app->session->setFlash('message','Such request already exists');  
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            $model->created_at = time();
            $model->created_by = Yii::$app->user->identity->id;

            $model->data = $model->getData();

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }else{
              Yii::$app->session->setFlash('type','error');  
              Yii::$app->session->setFlash('message',json_encode($model->getErrors()));  
            }
            // return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing PaymentRedemption model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->used == 1 || $model->approved_by != NULL) {
            Yii::$app->session->setFlash('type','error');  
            Yii::$app->session->setFlash('message','You cannot update approved and/or used entry.');  
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if ($model->load(Yii::$app->request->post())) {

            if ($model->exists()) {
                Yii::$app->session->setFlash('type','error');  
                Yii::$app->session->setFlash('message','Such request already exists');  
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            $model->created_at = time();
            $model->created_by = Yii::$app->user->identity->id;

            $model->data = $model->getData();

            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }else{
              Yii::$app->session->setFlash('type','error');  
              Yii::$app->session->setFlash('message',json_encode($model->getErrors()));  
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing PaymentRedemption model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);

        if ($model->used == 1 || $model->approved_by != NULL) {
            Yii::$app->session->setFlash('type','error');  
            Yii::$app->session->setFlash('message','You cannot delete approved and/or used entry.');  
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->delete();


        return $this->redirect(['index']);
    }

    /**
     * Finds the PaymentRedemption model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return PaymentRedemption the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = PaymentRedemption::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
