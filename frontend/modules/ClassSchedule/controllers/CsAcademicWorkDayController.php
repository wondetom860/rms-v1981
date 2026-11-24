<?php

namespace frontend\modules\ClassSchedule\controllers;

use Yii;
use frontend\modules\ClassSchedule\models\CsAcademicWorkDay;
use frontend\modules\ClassSchedule\models\CsClassTimeDistribution;
use frontend\modules\ClassSchedule\models\CsAcademicWorkDaySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * CsAcademicWorkDayController implements the CRUD actions for CsAcademicWorkDay model.
 */
class CsAcademicWorkDayController extends Controller
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
     * Lists all CsAcademicWorkDay models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CsAcademicWorkDaySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single CsAcademicWorkDay model.
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
     * Creates a new CsAcademicWorkDay model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CsAcademicWorkDay();

        if ($model->load(Yii::$app->request->post())) {

            $transaction = Yii::$app->db->beginTransaction();

            if ($model->checkExists()) {
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message','Such record already exists!');

                return $this->render('create', [
                    'model' => $model,
                ]);
            }else{
                $model->created_at = time();
                $model->created_by = Yii::$app->user->identity->id;

                $model->data = $model->getData();

                if ($model->save()) {
                    // distribute class time for the day mentioned.
                    try {
                        $model->distributeClassTime();
                        $transaction->commit();
                        Yii::$app->session->setFlash('type','success');
                        Yii::$app->session->setFlash('message','Record Saved successfuly');
                        return $this->redirect(['index']);

                    } catch (\Exception $e) {
                        $transaction->rollBack();
                        // var_dump($e);die();
                        Yii::$app->session->setFlash('type','error');
                        Yii::$app->session->setFlash('message',json_encode($e->getMessage()));
                    }
                    
                }else{
                    Yii::$app->session->setFlash('type','error');
                    Yii::$app->session->setFlash('message',json_encode($model->getErrors()));
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CsAcademicWorkDay model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            try {
                $model->updated_at = time();
                $model->updated_by = Yii::$app->user->identity->id;
                $model->distributeClassTime();
                $model->save();
                return $this->redirect(['index']);              
            } catch (\Exception $e) {
                // var_dump($e);die();
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message',json_encode($e->getMessage()));
            }
            
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing CsAcademicWorkDay model.
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
     * Finds the CsAcademicWorkDay model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CsAcademicWorkDay the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CsAcademicWorkDay::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
