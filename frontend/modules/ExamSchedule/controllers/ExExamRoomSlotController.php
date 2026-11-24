<?php

namespace frontend\modules\ExamSchedule\controllers;

use Yii;
use frontend\modules\ExamSchedule\models\ExExamRoomSlot;
use frontend\modules\ExamSchedule\models\ExExamRoomSlotSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ExExamRoomSlotController implements the CRUD actions for ExExamRoomSlot model.
 */
class ExExamRoomSlotController extends Controller
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

    public function actionPrintAttendanceSheet()
    {
        $exam_room_slot_id= Yii::$app->request->get('exam_room_slot_id');
        $model = ExExamRoomSlot::findOne(['id' => $exam_room_slot_id]);
        // $examinees = $model->exExaminees;
        $this->layout = '../../../../views/layouts/printLayout';
        return $this->render('__report/print-attendance-sheet', [
            'model' => $model,
            // 'examinees' => $examinees
        ]);
    }

    public function actionRemoveOffering(){
        $id = Yii::$app->request->post('id');
        $model = $this->findModel($id);
        $offeringId = Yii::$app->request->post('OfferingDetailId');
        $model->removeOffering($offeringId);

        $model->updated_at = time();
        $model->updated_by = Yii::$app->user->id;
        $model->getData();

        if($model->save()){
            Yii::$app->session->setFlash('success','Course/Offering removed from the list');
        }else{
            Yii::$app->session->setFlash('error','Unlisting failed, Error: '.json_encode($model->getErrors()));
        }

        return $this->redirect(['/ExamSchedule/ex-exam-room-slot/view','id' => $id]);

    }

    public function actionAddOfferings($id)
    {
        $model = $this->findModel($id);
        if (Yii::$app->request->post('offeringIds')) {
            // $model
            $model->offeringIds = Yii::$app->request->post('offeringIds');
            $model->appendOfferings();
            $model->getData();
            $model->updated_at = time();
            $model->updated_by = Yii::$app->user->identity->id;

            if ($model->save(false)) {
                Yii::$app->session->setFlash('success', 'Course scheduled successfully');
                return 1;
            }

            Yii::$app->session->setFlash('success', 'Course scheduling failed, Error: ' . json_encode($model->errors));
        }

        return $this->renderAjax('__report/add-new-offering', ['model' => $model]);
    }

    public function actionAddExamRoomSlot($date_time_schedule_id =   null)
    {
        $model = new ExExamRoomSlot();
        $modelDateTimeSlot = \frontend\modules\ExamSchedule\models\ExDateTimeSchedule::findOne(['id' => $date_time_schedule_id]);
        $model->date_time_slot_relId = $modelDateTimeSlot->id;
        // if ($model->load(Yii::$app->request->post())) {
        //     return $this->redirect(['view', 'id' => $model->id]);
        // }
        return $this->renderAjax('__report/add-new-exam-room-slot-form', [
            'model' => $model,
            'modelDateTimeSlot' => $modelDateTimeSlot
        ]);
    }

    /**
     * Lists all ExExamRoomSlot models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ExExamRoomSlotSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ExExamRoomSlot model.
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
     * Creates a new ExExamRoomSlot model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ExExamRoomSlot();

        if ($model->load(Yii::$app->request->post())) {
            if ($model->checkIfExists()) {
                Yii::$app->session->setFlash('error', Yii::t('rms', 'Exam Room Slot already exists.'));
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            // the course can not be assigned to two diffrent shifts/dates
            // check if scheduled to another shift:
            $model->created_at = time();
            $model->created_by = Yii::$app->user->id;
            $model->getData();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('rms', 'Exam Room Slot created successfully.'));
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('rms', 'Error creating Exam Room Slot. Error: ' . json_encode($model->errors)));
            }
            // return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ExExamRoomSlot model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            if ($model->checkIfExists($id)) {
                Yii::$app->session->setFlash('error', Yii::t('rms', 'Exam Room Slot already exists.'));
                return $this->render('create', [
                    'model' => $model,
                ]);
            }
            $model->updated_at = time();
            $model->updated_by = Yii::$app->user->id;
            $model->getData();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('rms', 'Exam Room Slot updated successfully.'));
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('rms', 'Error updating Exam Room Slot. Error: ' . json_encode($model->errors)));
            }
            // return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ExExamRoomSlot model.
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
     * Finds the ExExamRoomSlot model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExExamRoomSlot the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExExamRoomSlot::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
