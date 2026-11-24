<?php

namespace frontend\controllers;

use Yii;
use backend\models\EthnicGroup;
use backend\models\EthnicGroupSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EthnicGroupController implements the CRUD actions for EthnicGroup model.
 */
class EthnicGroupController extends Controller
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
                       'actions' => ['index','create', 'update', 'delete','view'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
                    [
                       'actions' => ['index','get-list'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['@'],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all EthnicGroup models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EthnicGroupSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetList($stdId){
        $stdModel = \backend\models\Student::findOne(intval($stdId));
        return $this->renderPartial('__report/list',['studentModel' => $stdModel,'model' => EthnicGroup::find()->all()]);
    }

    /**
     * Displays a single EthnicGroup model.
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
     * Creates a new EthnicGroup model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EthnicGroup();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->exists()) {
                \Yii::$app->session->setFlash('type','error');
                \Yii::$app->session->setFlash('message','Such record already exists!');
            }else{
                $model->created_by = Yii::$app->user->id;
                $model->created_at = time();
                $model->remark = 1;
                $model->getData();

                if ($model->save()) {
                    \Yii::$app->session->setFlash('type','success');
                    \Yii::$app->session->setFlash('message','Record registered successfully, this is detail view.');
                    return $this->redirect(['view', 'id' => $model->id]);   
                }else{
                    \Yii::$app->session->setFlash('type','error');
                    \Yii::$app->session->setFlash('message','Error while recording: '.json_encode($model->getErrors()));
                }
            }
            
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EthnicGroup model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->exists($id)) {
                \Yii::$app->session->setFlash('type','error');
                \Yii::$app->session->setFlash('message','Such record already exists!');
            }else{
                $model->updated_by = Yii::$app->user->id;
                $model->updated_at = time();
                $model->remark = 1;
                $model->getData();

                if ($model->save()) {
                    \Yii::$app->session->setFlash('type','success');
                    \Yii::$app->session->setFlash('message','Record updated successfully.');
                    return $this->redirect(['view', 'id' => $model->id]);   
                }else{
                    \Yii::$app->session->setFlash('type','error');
                    \Yii::$app->session->setFlash('message','Error while recording: '.json_encode($model->getErrors()));
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing EthnicGroup model.
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
     * Finds the EthnicGroup model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EthnicGroup the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EthnicGroup::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
