<?php

namespace frontend\modules\ExitExam\controllers;

use Yii;
use frontend\modules\ExitExam\models\EtSetting;
use frontend\modules\ExitExam\models\EtSettingSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * EtSettingController implements the CRUD actions for EtSetting model.
 */
class EtSettingController extends Controller
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
                        'actions' => [],
                        'allow' => true,
                        'roles' => ['Admin', 'Registrar'],
                    ]
                ]
            ]
        ];
    }

    /**
     * Lists all EtSetting models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new EtSettingSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single EtSetting model.
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
     * Creates a new EtSetting model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new EtSetting();

        if ($model->load(Yii::$app->request->post())) {
            $model->created_at = time();
            $model->created_by = Yii::$app->user->id;
            $model->getData();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('rms', 'Setting Created Successfully'));
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('rms', 'Setting Creation Failed') . ' Error: ' . json_encode($model->getErrors()));
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing EtSetting model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->locked == 1) {
            Yii::$app->session->setFlash('error', Yii::t('rms', 'Setting Locked, Cannot be updated'));
            return $this->redirect(['index']);
        }

        if ($model->load(Yii::$app->request->post())) {
            $model->updated_at = time();
            $model->updated_by = Yii::$app->user->id;
            $model->getData();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('rms', 'Setting Updated Successfully'));
            } else {
                Yii::$app->session->setFlash('error', Yii::t('rms', 'Setting Update Failed') . ' Error: ' . json_encode($model->getErrors()));
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing EtSetting model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionDelete($id)
    {
        $model = $this->findModel($id);
        if ($model->locked == 1) {
            Yii::$app->session->setFlash('error', Yii::t('rms', 'Setting Locked, Cannot be deleted'));
            return $this->redirect(['index']);
        }
        $model->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the EtSetting model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return EtSetting the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = EtSetting::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
