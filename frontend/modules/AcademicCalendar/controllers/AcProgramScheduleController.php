<?php

namespace frontend\modules\AcademicCalendar\controllers;

use Yii;
use frontend\modules\AcademicCalendar\models\AcProgramSchedule;
use frontend\modules\AcademicCalendar\models\AcActivePrograms;
use frontend\modules\AcademicCalendar\models\AcProgramScheduleSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * AcProgramScheduleController implements the CRUD actions for AcProgramSchedule model.
 */
class AcProgramScheduleController extends Controller
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
                       'actions' => ['index','change-status','get-report-print-backlog','get-program-schedule','get-program-schedule-per-activity'],
                       'allow' => true,
                       'roles' => ['Registrar'],
                   ],
               ]
           ]
        ];
    }

    public function actionGetProgramSchedulePerActivity(){
        $activityId = isset($_POST['activityId']) && !empty($_POST['activityId']) ? intval($_POST['activityId']) : die('INVALID_REQUEST');
        $ayId = isset($_POST['ayId']) && !empty($_POST['ayId']) ? intval($_POST['ayId']) : die('INVALID_REQUEST');

        $records = AcActivePrograms::find()->where(['academic_year_relId' => $ayId])->all();
        $model = new AcProgramSchedule();

        // $model->active_program_relId = $ayId;
        $model->action_relId = $activityId;

        return $this->renderPartial('__report/programs',['records' => $records,'model' => $model,'ayid' => $ayId,'activityId' => $activityId]);
    }

    public function actionGetProgramSchedule(){
        $ayId = isset($_POST['ayId']) && !empty($_POST['ayId']) ? intval($_POST['ayId']) : die('INVALID_REQUEST');

        $records = AcActivePrograms::find()->where(['academic_year_relId' => $ayId])->all();

        $model = new AcProgramSchedule();
        $modelAction = new \backend\models\AcademicCalanderAction(); 

        return $this->renderPartial('__report/index',['records' => $records,'model' => $model,'modelAction' => $modelAction]);

        // return $this->renderPartial('__report/index',['records' => $model]);
    }

    /**
     * Lists all AcProgramSchedule models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new AcProgramScheduleSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    /**
     * Displays a single AcProgramSchedule model.
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

    public function actionCreate2(){
        $model = new AcProgramSchedule();
        if ($model->load(Yii::$app->request->post())) {
            $model->start_date = strtotime($model->start_date);
            $model->end_date = strtotime($model->end_date) + (24*60*60)-1;//should be active on last day.

            if ($model->checkIfExists()) {
                Yii::$app->session->setFlash('type', 'error');
                Yii::$app->session->setFlash('message','Such schedule already exists.');
            }
            // else if(!$model->datesInAcademicYearInterval()){
            //     //checks if the schedule dates are with the inteval of academic year.
            //     Yii::$app->session->setFlash('type', 'error');
            //     Yii::$app->session->setFlash('message','Date interval out of academic year of selected academic year, Please check your start and end date');
            // }
            else{
                $model->last_extend = 0;
                $model->last_extend_status = 0;
                $model->created_by = Yii::$app->user->identity->id;
                $model->created_at = time();
                $model->getData();
                if ($model->save(false)) {
                    $__id = $model->active_program_relId;
                    $model = new AcProgramSchedule();
                    $model->active_program_relId = $__id;
                    Yii::$app->session->setFlash('type', 'success');
                    Yii::$app->session->setFlash('message','program activity scheduled successfully.');
                }else{
                    Yii::$app->session->setFlash('type', 'error');
                    Yii::$app->session->setFlash('message','Error occured while storing progam schedule(s). Please check the error: '.json_encode($model->getErrors()));
                }
            }
        }

        return $this->redirect(['/AcademicCalendar/ac-active-programs/view', 'id' => $model->active_program_relId ]);
    }

    /**
     * Creates a new AcProgramSchedule model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate($pid = null)
    {
        $model = new AcProgramSchedule();
        if (!is_null($pid)) {
            $model->active_program_relId = intval($pid);
            return $this->render('create', [
                'model' => $model,
            ]);
        }

        if ($model->load(Yii::$app->request->post())) {

            $_programs = json_decode($model->__selected_programs);
            
            if (count($_programs) > 0) {//checks if any program is selected.
                $model->start_date = strtotime($model->start_date);
                $model->end_date = strtotime($model->end_date);

                if ($model->validDateInterval()) {//start and end dates of the schedule
                    
                    foreach ($_programs as $program) {
                        $modelNew = new AcProgramSchedule();
                        $modelNew->action_relId = $model->action_relId;
                        $modelNew->start_date = $model->start_date;
                        $modelNew->end_date = $model->end_date + (24*60*60)-1;//should be active on last day.
                        $modelNew->active_program_relId = $program;

                        if ($modelNew->checkIfExists()) {
                            \Yii::$app->session->setFlash('type', 'warning');
                            \Yii::$app->session->setFlash('message','Some schedules skipped, because they are already scheduled for the academic year selected.');
                            continue;
                        }else if(!$modelNew->datesInAcademicYearInterval()){
                            //checks if the schedule dates are with the inteval of academic year.
                            \Yii::$app->session->setFlash('type', 'error');
                            \Yii::$app->session->setFlash('message','Date interval out of academic year of selected academic year, Please check your start and end date');
                            break;
                        }else{
                            $modelNew->last_extend = 0;
                            $modelNew->last_extend_status = 0;
                            $modelNew->created_by = \Yii::$app->user->identity->id;
                            $modelNew->created_at = time();
                            $modelNew->getData();
                            if ($modelNew->save(false)) {
                                \Yii::$app->session->setFlash('type', 'success');
                                \Yii::$app->session->setFlash('message','program activity scheduled successfully.');
                            }else{
                                \Yii::$app->session->setFlash('type', 'error');
                                \Yii::$app->session->setFlash('message','Error occured while storing progam schedule(s). Please check the error: '.json_encode($modelNew->getErrors()));
                            }
                        }
                    }
                }else{
                    \Yii::$app->session->setFlash('type', 'error');
                    \Yii::$app->session->setFlash('message','Invalid date interval, please check your start and date.');
                    return $this->redirect(['/AcademicCalendar']);
                }
            }else{
                \Yii::$app->session->setFlash('type', 'error');
                \Yii::$app->session->setFlash('message','Please selecta atleast one program to schedule.');
            }
            return $this->redirect(['/AcademicCalendar']);
        }
    }

    /**
     * Updates an existing AcProgramSchedule model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $model->start_date = strtotime($model->start_date);
            $model->end_date = strtotime($model->end_date) + (24*60*60)-1;

            //&& $model->datesInAcademicYearInterval()
            if ($model->validDateInterval()) {
                if ($model->checkIfExists($model->id)) {
                    \Yii::$app->session->setFlash('type', 'warning');
                    \Yii::$app->session->setFlash('message','Such enrty already exists.');
                }else{
                    $model->updated_by = \Yii::$app->user->identity->id;
                    $model->updated_at = time();
                    $model->getData();
                    if ($model->save(false)) {
                        \Yii::$app->session->setFlash('type', 'success');
                        \Yii::$app->session->setFlash('message','program activity scheduled successfully.');
                        return $this->redirect('index');
                    }else{
                        \Yii::$app->session->setFlash('type', 'error');
                        \Yii::$app->session->setFlash('message','Error occured while storing progam schedule(s). Please check the error: '.json_encode($model->getErrors()));
                    }
                }
            }else{
                \Yii::$app->session->setFlash('type', 'error');
                \Yii::$app->session->setFlash('message','Invalid date interval, please check your start and date. Academic year schedule is '."[{$model->activeProgramRel->academicYearRel->getSchedule()}]");
            }
        }

        return $this->render('update', [
            'model' => $model,
        ]);
    }

    /**
     * Deletes an existing AcProgramSchedule model.
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
     * Finds the AcProgramSchedule model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return AcProgramSchedule the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = AcProgramSchedule::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
