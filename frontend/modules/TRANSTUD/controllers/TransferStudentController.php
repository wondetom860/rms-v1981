<?php

namespace frontend\modules\TRANSTUD\controllers;

use frontend\modules\TRANSTUD\models\ExemptedCourse;
use Yii;
use frontend\modules\TRANSTUD\models\TransferStudent;
use frontend\modules\TRANSTUD\models\TransferStudentSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * TransferStudentController implements the CRUD actions for TransferStudent model.
 */
class TransferStudentController extends Controller
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
                    ],
                    [
                        'actions' => ['index', 'view', 'unapproved-requests', 'add-exempted-course'],
                        'allow' => true,
                        'roles' => ['Registrar'],
                    ]
                ]
            ]
        ];
    }

    public function actionApproveTsRecord($id, $option)
    {
        $model = $this->findModel($id);
        if ($model->approveTsRecord($option)) {
            Yii::$app->session->setFlash('success', 'Transfer student record approved successfully.');
        } else {
            Yii::$app->session->setFlash('error', 'Failed to change transfer student approval record.');
        }
        return $this->redirect(['view', 'id' => $model->id]);
    }


    public function actionAddExemptedCourse($tsId)
    {
        $modelTs = $this->findModel($tsId);
        $model = new ExemptedCourse();
        $model->ts_relid = $modelTs->id;
        if (Yii::$app->request->isAjax) {
            return $this->renderAjax('/exempted-course/create', [
                'model' => $model,
                // 'modelTs' => $modelTs,
            ]);
        }
    }

    /**
     * Lists all TransferStudent models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new TransferStudentSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single TransferStudent model.
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
     * Creates a new TransferStudent model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new TransferStudent();

        if ($model->load(Yii::$app->request->post())) {
            $exists = $model->checkIfExists($model->std_relId);
            if ($exists) {
                Yii::$app->session->setFlash('warning', 'Already exists');
                return $this->redirect(['view', 'id' => $exists->id]);
            } else {
                $model->created_at = time();
                $model->created_by = Yii::$app->user->identity->id;
                $model->getData();

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Created entry succesfully');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    Yii::$app->session->setFlash('warning', 'Creating failed, Error: ' . json_encode($model->errors));
                }
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing TransferStudent model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $exists = $model->checkIfExists($model->std_relId, $id);
            if ($exists) {
                Yii::$app->session->setFlash('warning', 'Already exists');
                return $this->redirect(['view', 'id' => $exists->id]);
            } else {
                $model->updated_at = time();
                $model->updated_by = Yii::$app->user->identity->id;
                $model->getData();

                if ($model->save()) {
                    Yii::$app->session->setFlash('success', 'Updated entry succesfully');
                    return $this->redirect(['view', 'id' => $model->id]);
                } else {
                    Yii::$app->session->setFlash('warning', 'Updating failed, Error: ' . json_encode($model->errors));
                }
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing TransferStudent model.
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
     * Finds the TransferStudent model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return TransferStudent the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = TransferStudent::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
