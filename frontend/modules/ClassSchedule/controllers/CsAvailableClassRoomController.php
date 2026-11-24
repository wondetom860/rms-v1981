<?php

namespace frontend\modules\ClassSchedule\controllers;

use Yii;
use frontend\modules\ClassSchedule\models\CsAvailableClassRoom;
use frontend\modules\ClassSchedule\models\CsAvailableClassRoomSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CsAvailableClassRoomController implements the CRUD actions for CsAvailableClassRoom model.
 */
class CsAvailableClassRoomController extends Controller
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
                       'actions' => [],
                       'allow' => true,
                       'roles' => ['Admin'],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all CsAvailableClassRoom models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CsAvailableClassRoomSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CsAvailableClassRoom model.
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
     * Creates a new CsAvailableClassRoom model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CsAvailableClassRoom();

        if ($model->load(Yii::$app->request->post())) {

            $model->created_at = time();
            $model->created_by = Yii::$app->user->identity->id;

            // check if similar exists
            if ($model->checkExists()) {
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message','Such Class Room information already exists!');

                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            // generate checksum
            $model->data = $model->getData();

            if ($model->save()) {
                Yii::$app->session->setFlash('type','success');
                Yii::$app->session->setFlash('message','Record Saved successfuly');

                return $this->redirect(['index']);
            }else{
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message',json_encode($model->getErrors()));

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
     * Updates an existing CsAvailableClassRoom model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $model->updated_at = time();
            $model->updated_by = Yii::$app->user->identity->id;

            
            // generate checksum
            $model->data = $model->getData();

            if ($model->save()) {
                Yii::$app->session->setFlash('type','success');
                Yii::$app->session->setFlash('message','Record Updated successfuly');

                return $this->redirect(['view', 'id' => $model->id]);
            }else{
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message',json_encode($model->getErrors()));

                return $this->render('update', [
                    'model' => $model,
                ]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing CsAvailableClassRoom model.
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
     * Finds the CsAvailableClassRoom model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CsAvailableClassRoom the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CsAvailableClassRoom::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
