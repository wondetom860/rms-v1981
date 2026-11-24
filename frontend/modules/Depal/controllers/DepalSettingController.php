<?php

namespace frontend\modules\Depal\controllers;

use Yii;
use frontend\modules\Depal\models\DepalSetting;
use frontend\modules\Depal\models\DepalSettingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * DepalSettingController implements the CRUD actions for DepalSetting model.
 */
class DepalSettingController extends Controller
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
                       'actions' => ['index', 'update', 'delete','view','create'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Admin',],
                   ],
                   [
                       'actions' => ['index','view','get-grouped-students'],
                       'allow' => true,
                       // Allow  admins to create, update, and delete, and view (index)
                       'roles' => ['Registrar','AcademicDean','DepartmentHead'],
                   ]
               ]
           ]
        ];
    }

    /**
     * Lists all DepalSetting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new DepalSettingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single DepalSetting model.
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
     * Creates a new DepalSetting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new DepalSetting();

        if ($model->load(Yii::$app->request->post())) {

            if ($model->alreadyExists()) {
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message',"Setting already exists for the selected section");
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            $model->start_date = strtotime($model->startdate);
            $model->end_date = strtotime($model->enddate);
            $model->created_at = time();
            $model->created_by = Yii::$app->user->id;
            $model->getScheduleStatus(false);
            $model->getData();

            if ($model->save()) {
                return $this->redirect('index');
            }else{
                Yii::$app->session->setFlash('type' , 'error');
                Yii::$app->session->setFlash('message', json_encode($model->getErrors()));
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing DepalSetting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if($model->status == DepalSetting::STATUS_CLOSED){
            \Yii::$app->session->setFlash('type','error');
            \Yii::$app->session->setFlash('message','Updting closed record is NOT allowed');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->start_date = strtotime($model->startdate);
            $model->end_date = strtotime($model->enddate);
            $model->updated_at = time();
            $model->updated_by = Yii::$app->user->id;
            $model->getData();
            if ($model->save()) {
                return $this->redirect(['view', 'id' => $model->id]);
            }            
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing DepalSetting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if($model->status == DepalSetting::STATUS_CLOSED){
            \Yii::$app->session->setFlash('type','error');
            \Yii::$app->session->setFlash('message','Deleting closed record is NOT allowed');
            return $this->redirect(['view', 'id' => $model->id]);
        }

        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the DepalSetting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return DepalSetting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = DepalSetting::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
