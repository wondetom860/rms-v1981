<?php

namespace frontend\modules\AcStEva\controllers;

use Yii;
use frontend\modules\AcStEva\models\AseSemesterEvaluation;
use frontend\modules\AcStEva\models\AseSemesterEvaluationSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AseSemesterEvaluationController implements the CRUD actions for AseSemesterEvaluation model.
 */
class AseSemesterEvaluationController extends Controller
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
                        'roles' => ['Admin'],
                    ],
                    [
                        'actions' => ['index', 'view', 'create', 'update', 'delete'],
                        'allow' => true,
                        'roles' => ['AcademicDean'],
                    ],
                    [
                        'actions' => ['index', 'view','create'],
                        'allow' => true,
                        'roles' => ['Registrar','Admin'],
                    ],
                    [
                        'actions' => ['index','view'],
                        'allow' => true,
                        'roles' => ['DepartmentHead'],
                    ],
                ]
            ]
        ];
    }

    /**
     * Lists all AseSemesterEvaluation models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AseSemesterEvaluationSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AseSemesterEvaluation model.
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
     * Creates a new AseSemesterEvaluation model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new AseSemesterEvaluation();

        if ($model->load(Yii::$app->request->post())) {

            $ex = $model->checkIfExists();
            if ($ex) {
                // var_dump($ex);die();
                Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('message', 'Such entry already exists, if you want to add new criteria under this type, click <a href="/AcStEva/ase-semester-evaluation/update?id=' . $ex->id . '">here</a>');
                return $this->redirect(['/AcStEva/ase-semester-evaluation/view', 'id' => $ex->id]);
            }

            $model->start_date = strtotime($model->startdate);
            $model->end_date = strtotime($model->enddate);
            $model->created_at = time();
            $model->created_by = Yii::$app->user->id;
            $model->getData();

            if ($model->save()) {
                // send email for all dept heads.
                $model->sendNotificationMail();
                Yii::$app->session->setFlash('type', 'success');
                Yii::$app->session->setFlash('message', 'Record registered successfully');
                return $this->redirect(['view', 'id' => $model->id]);
            }

            // var_dump($model->start_date);

            Yii::$app->session->setFlash('type', 'error');
            Yii::$app->session->setFlash('message', 'Record registering failed, ERROR: ' . json_encode($model->getErrors()));
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing AseSemesterEvaluation model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $ex = $model->checkIfExists($model->id);
            if ($ex && count($ex) > 0) {
                Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('message', 'Such record already exists');
                return $this->redirect(['view', 'id' => $ex->id]);
            }
            $model->updated_at = time();
            $model->updated_by = Yii::$app->user->id;
            $model->start_date = strtotime($model->startdate);
            $model->end_date = strtotime($model->enddate);
            if ($model->save()) {
                Yii::$app->session->setFlash('type', 'success');
                Yii::$app->session->setFlash('message', 'Record Updated successfully');
                return $this->redirect(['view', 'id' => $model->id]);
            }
            Yii::$app->session->setFlash('type', 'error');
            Yii::$app->session->setFlash('message', 'Record registering failed, ERROR: ' . json_encode($model->getErrors()));
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AseSemesterEvaluation model.
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
     * Finds the AseSemesterEvaluation model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AseSemesterEvaluation the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AseSemesterEvaluation::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
