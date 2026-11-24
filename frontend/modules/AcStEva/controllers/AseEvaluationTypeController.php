<?php

namespace frontend\modules\AcStEva\controllers;

use Yii;
use frontend\modules\AcStEva\models\AseEvaluationType;
use frontend\modules\AcStEva\models\AseEvaluationTypeSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AseEvaluationTypeController implements the CRUD actions for AseEvaluationType model.
 */
class AseEvaluationTypeController extends Controller
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
                   [
                       'actions' => ['index','view'],
                       'allow' => true,
                       'roles' => ['Registrar','AcademicDean'],
                   ],
               ]
           ]
        ];
    }

    /**
     * Lists all AseEvaluationType models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AseEvaluationTypeSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AseEvaluationType model.
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
     * Creates a new AseEvaluationType model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AseEvaluationType();

        if ($model->load(Yii::$app->request->post())) {
            $model->created_by = Yii::$app->user->id;
            $model->created_at = time();
            $model->getData();

            if ($model->save()) {
                Yii::$app->session->setFlash('type','success');
                Yii::$app->session->setFlash('message','Record registered successfully');
                return $this->redirect('index');
            }

            Yii::$app->session->setFlash('type','error');
            Yii::$app->session->setFlash('message','Record registering failed, ERROR: '.json_encode($model->getErrors()));
            return $this->render('create', [
                'model' => $model,
            ]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AseEvaluationType model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->updated_by = Yii::$app->user->id;
            $model->updated_at = time();
            $model->getData();

            if ($model->save()) {
                Yii::$app->session->setFlash('type','success');
                Yii::$app->session->setFlash('message','Record updated successfully');
                return $this->redirect(['view', 'id' => $model->id]);
            }else{
                Yii::$app->session->setFlash('type','error');
                Yii::$app->session->setFlash('message','Record updating failed, ERROR: '.json_encode($model->getErrors()));
                return $this->redirect(['view', 'id' => $model->id]);
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AseEvaluationType model.
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
     * Finds the AseEvaluationType model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AseEvaluationType the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AseEvaluationType::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
