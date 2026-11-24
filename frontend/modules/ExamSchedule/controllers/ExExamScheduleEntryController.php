<?php

namespace frontend\modules\ExamSchedule\controllers;

use Yii;
use frontend\modules\ExamSchedule\models\ExExamScheduleEntry;
use frontend\modules\ExamSchedule\models\ExExamScheduleEntrySearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * ExExamScheduleEntryController implements the CRUD actions for ExExamScheduleEntry model.
 */
class ExExamScheduleEntryController extends Controller
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

    public function actionPrintExamSchedule(){
        $entryId = Yii::$app->request->get('entry_id');
        $entryModel = $this->findModel($entryId);
        $_SESSION['report'] = $entryModel->getScehduleEntryHeader();

        $this->layout = '../../../../views/layouts/printLayout';
        return $this->render('__report/exam-schedule-printout',['model' => $entryModel]);
    }

    public function actionPrintStaffSchedule(){
        $entryId = Yii::$app->request->get('entry_id');
        $entryModel = $this->findModel($entryId);
        $_SESSION['report'] = $entryModel->getScehduleEntryHeader();

        $this->layout = '../../../../views/layouts/printLayout';
        return $this->render('__report/staff-schedule-printout',['model' => $entryModel]);
    }

    /**
     * Lists all ExExamScheduleEntry models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new ExExamScheduleEntrySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single ExExamScheduleEntry model.
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
     * Creates a new ExExamScheduleEntry model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ExExamScheduleEntry();

        if ($model->load(Yii::$app->request->post())) {

            $model->start_date = strtotime($model->start_date_str);
            $model->end_date = strtotime($model->end_date_str) + (86400 - 1);
            $error_message = $model->checkValidDate();

            if (!is_null($error_message)) {
                Yii::$app->session->setFlash('error', Yii::t('rms', $error_message));
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            if ($model->checkIfExists()) {
                Yii::$app->session->setFlash('error', Yii::t('rms', 'Exam Schedule Entry already exists.'));
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            $model->created_at = time();
            $model->created_by = Yii::$app->user->id;
            $model->getData();

            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('rms', 'Exam Schedule Entry has been created successfully.'));
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('rms', 'Error while creating Exam Schedule Entry: ERROR:' . json_encode($model->errors)));
            }
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing ExExamScheduleEntry model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {
            $model->start_date = strtotime($model->start_date_str);
            $model->end_date = strtotime($model->end_date_str);
            $error_message = $model->checkValidDate($forUpdate = true);

            if (!is_null($error_message)) {
                Yii::$app->session->setFlash('error', Yii::t('rms', $error_message));
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            if ($model->checkIfExists($model->id)) {
                Yii::$app->session->setFlash('error', Yii::t('rms', 'Exam Schedule Entry already exists.'));
                return $this->render('create', [
                    'model' => $model,
                ]);
            }

            $model->updated_at = time();
            $model->updated_by = Yii::$app->user->id;
            $model->getData();
            if ($model->save()) {
                Yii::$app->session->setFlash('success', Yii::t('rms', 'Exam Schedule Entry has been updated successfully.'));
                return $this->redirect(['view', 'id' => $model->id]);
            } else {
                Yii::$app->session->setFlash('error', Yii::t('rms', 'Error while updating Exam Schedule Entry: ERROR:' . json_encode($model->errors)));
            }
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing ExExamScheduleEntry model.
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
     * Finds the ExExamScheduleEntry model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ExExamScheduleEntry the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ExExamScheduleEntry::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
