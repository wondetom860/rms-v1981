<?php

namespace frontend\modules\ExamSchedule\controllers;

use Yii;
use frontend\modules\ExamSchedule\models\ExDateTimeSchedule;
use frontend\modules\ExamSchedule\models\ExDateTimeScheduleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ExDateTimeScheduleController implements the CRUD actions for ExDateTimeSchedule model.
 */
class ExDateTimeScheduleController extends Controller
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
                        'roles' => ['Admin','Registrar'],
                    ]
                ]
            ]
        ];
    }

    public function actionRemoveStandby(){
        if (Yii::$app->request->post('id')) {
            $model = $this->findModel(Yii::$app->request->post('id'));
            $model->removeStdbyUser(Yii::$app->request->post('userId'));
            $model->updated_at = time();
            $model->updated_by = Yii::$app->user->id;
            $model->getData();
            if($model->save(false)){
                return 1;
            }else{
                return json_encode($model->errors);
            }
        }
        return 0;
    }

    public function actionAddSupervisor($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->post('supervisor_user_id')) {
            $model->appendSupervisorUser(Yii::$app->request->post('supervisor_user_id'));
            $model->updated_at = time();
            $model->updated_by = Yii::$app->user->id;
            $model->getData();
            $model->save(false);
            return 1;
        }
        return $this->renderAjax('__report/add-new-supervisor-form', [
            'model' => $model,
        ]);
    }

    public function actionRemoveSupervisor(){
        if (Yii::$app->request->post('id')) {
            $model = $this->findModel(Yii::$app->request->post('id'));
            $model->removeSupervisorUser(Yii::$app->request->post('userId'));
            $model->updated_at = time();
            $model->updated_by = Yii::$app->user->id;
            $model->getData();
            if($model->save(false)){
                return 1;
            }else{
                return json_encode($model->errors);
            }
        }
        return 0;
    }

    public function actionAddStandby($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->post('standby_user_id')) {
            $model->appendStdbyUser(Yii::$app->request->post('standby_user_id'));
            $model->updated_at = time();
            $model->updated_by = Yii::$app->user->id;
            $model->getData();
            $model->save(false);
            return 1;
        }
        return $this->renderAjax('__report/add-new-standby-form', [
            'model' => $model,
        ]);
    }

    /**
     * Lists all ExDateTimeSchedule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ExDateTimeScheduleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ExDateTimeSchedule model.
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
     * Creates a new ExDateTimeSchedule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ExDateTimeSchedule();

        if ($model->load(Yii::$app->request->post())) {

            $model->date = strtotime($model->date_str);
            $model->start_time = $model->start_time_str;
            $model->end_time = $model->end_time_str;
            // $now = strtotime(date('H:i', $model->date));
            // $start_time
            $model->start_time = strtotime(date_format(date_create($model->start_time), 'h:i'));
            $model->end_time = strtotime(date_format(date_create($model->end_time), 'h:i'));

            if (($model->date) < strtotime(date('Y-m-d'))) {
                // one hr = 3600 seconds
                Yii::$app->session->setFlash('error', Yii::t('rms', 'Date cannot be in the past'));
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            if ($model->checkIfExists()) {
                Yii::$app->session->setFlash('error', Yii::t('rms', 'This date already exists'));
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            if ($model->start_time >= $model->end_time) {
                Yii::$app->session->setFlash('error', Yii::t('rms', 'Start time cannot be greater than or equal to end time [' . $model->start_time . ' | ' . $model->end_time . ']'));
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            $model->created_at = time();
            $model->created_by = Yii::$app->user->id;
            $model->getData();

            if ($model->save()) {
                if (Yii::$app->request->isAjax) {
                    $this->redirect(['/ExamSchedule/ex-date-time-schedule/view', 'id' => $model->id]);
                } else {
                    Yii::$app->session->setFlash('success', Yii::t('rms', 'Data saved successfully'));
                }
                return $this->redirect(['/ExamSchedule/ex-date-time-schedule/view', 'id' => $model->id]);
            } else {
                if (Yii::$app->request->isAjax) {
                    return 'Data not saved, Error: ' . json_encode($model->errors);
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('rms', 'Data not saved, Error: ' . json_encode($model->errors)));
                }
            }
            return $this->redirect(['/ExamSchedule/ex-exam-schedule-entry/index', 'id' => $model->exam_schedule_entry_relId]);
        }

        return Yii::$app->request->isAjax ? $this->renderAjax('create', [
            'model' => $model,
        ]) : $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ExDateTimeSchedule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->date = strtotime($model->date_str);
            $model->start_time = strtotime($model->start_time_str);
            $model->end_time = strtotime($model->end_time_str);

            if (($model->date) < strtotime(date('Y-m-d'))) {
                Yii::$app->session->setFlash('error', Yii::t('rms', 'Date cannot be in the past'));
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            if ($model->checkIfExists($id)) {
                Yii::$app->session->setFlash('error', Yii::t('rms', 'This date already exists'));
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            if ($model->start_time >= $model->end_time) {
                Yii::$app->session->setFlash('error', Yii::t('rms', 'Start time cannot be greater than or equal to end time'));
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            $model->updated_at = time();
            $model->updated_by = Yii::$app->user->id;
            $model->getData();

            if ($model->save()) {
                if (Yii::$app->request->isAjax) {
                    return 1;
                } else {
                    Yii::$app->session->setFlash('success', Yii::t('rms', 'Data updated successfully'));
                    $this->redirect(['view', 'id' => $model->id]);
                }
            } else {
                if (Yii::$app->request->isAjax) {
                    return 'Data not updated, Error: ' . json_encode($model->errors);
                } else {
                    Yii::$app->session->setFlash('error', Yii::t('rms', 'Data not updated, Error: ' . json_encode($model->errors)));
                }
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ExDateTimeSchedule model.
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
     * Finds the ExDateTimeSchedule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExDateTimeSchedule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExDateTimeSchedule::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
