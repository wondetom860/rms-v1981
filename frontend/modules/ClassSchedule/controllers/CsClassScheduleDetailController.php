<?php

namespace frontend\modules\ClassSchedule\controllers;

use Yii;
use frontend\modules\ClassSchedule\models\CsClassScheduleDetail;
use frontend\modules\ClassSchedule\models\CsClassTimeDistribution;
use frontend\modules\ClassSchedule\models\CsClassScheduleDetailSearch;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;

/**
 * This is the model class for table "cs_class_schedule_detail".
 *
 * @property int $id
 * @property int $gset_relId
 * @property int $class_time_distrib_relId
 * @property int $coff2det_relId
 * @property int $room_relId
 * @property string $class_type => ['lecture','Tutorial','Laboratory']
 * @property int $created_at
 * @property int $created_by
 * @property int|null $updated_at
 * @property int|null $updated_by
 * @property string $data
 *
 * @property CsClassTimeDistribution $classTimeDistribRel
 * @property CourseOffering2Detail $coff2detRel
 * @property User $createdBy
 * @property WGroupSetup $gsetRel
 * @property User $updatedBy
 * @property CsAvailableClassRoom $roomRel
 */


/**
 * CsClassScheduleDetailController implements the CRUD actions for CsClassScheduleDetail model.
 */
class CsClassScheduleDetailController extends Controller
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
                       'actions' => ['get-schedule','create2','update','get-free-slot','get-schedule-report'],
                       'allow' => true,
                       'roles' => ['DepartmentHead'],
                   ],
               ]
           ]
        ];
    }

    public function actionGetScheduleReport(){
        $queryString = isset($_GET['queryString']) && !empty($_GET['queryString']) ? (string)$_GET['queryString'] : die('Invalid request');

        $queryString = json_decode($queryString);

        $type = intval($queryString->type);
        $coffdetId = intval($queryString->coffdet);

        $this->layout = '../../../../views/layouts/printLayout';

        $model = new CsClassScheduleDetail();
        $coffdetModel = \backend\models\CourseOffering2Detail::findOne($coffdetId);

        return $this->render('__report/time_schedule_report',['model' => $model,'type' => $type,'coffdetModel' => $coffdetModel]);
    }

    /**
     * Lists all CsClassScheduleDetail models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new CsClassScheduleDetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);
    }

    public function actionGetSchedule(){//this action is for adding /creating new schedule --
        $coffdetId = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die('InvalId ReqUest');
        $type = isset($_POST['class_type']) && !empty($_POST['class_type']) ? (string)$_POST['class_type'] : die('InvalId ReqUest, Uknown Class Type');
        $room = isset($_POST['room']) && !empty($_POST['room']) ? (string)$_POST['room'] : die('InvalId ReqUest, Uknown room, Please select class room');

        $model = new CsClassScheduleDetail();

        return $this->renderPartial('__report/time-schedule',['model' => $model,'academicWorkDay' => $model->getAcademicWorkDay(),'coffdetModel' => $model->getCoff2detObj($coffdetId),'room' => $room,'type' => $type]);

    }

    public function actionGetFreeSlot(){
        $gset = isset($_POST['gset']) && !empty($_POST['gset']) ? intval($_POST['gset']) : die('Invalid request, group Uknown');
        $coffdetId = isset($_POST['coffdetId']) && !empty($_POST['coffdetId']) ? intval($_POST['coffdetId']) : die('Invalid request, Course Offering Uknown');
        $room = isset($_POST['room']) && !empty($_POST['room']) ? intval($_POST['room']) : die('Invalid request, Class Room Uknown');
        $type = isset($_POST['type']) && !empty($_POST['type']) ? (string)$_POST['type'] : die('Invalid request, Class Type Uknown');

        $model = new CsClassScheduleDetail();

        $slots = CsClassTimeDistribution::find()->all();

        $reply ="";

        if ($slots && count($slots) > 0) {
            foreach ($slots as $keyDist) {
                if ($keyDist->luncTime()) {continue; }   

                $checkSlot = $model->getSchedule($coffdetId,$keyDist->id,$room);
                if ($checkSlot && count($checkSlot) > 0) {
                    continue;
                }else{
                    // $radio = "<input class='pull-right radio___' id='radio__{$keyDist->id}' type='radio' title='Set class time to this' onchange='' >";
                    $reply .= "<p style='cursor:pointer' onclick='setScheduleTime({$keyDist->id});return false;' class='p__ bg-default btn-block' id='p__{$keyDist->id}'>{$keyDist->workDayRel->day_name} - {$keyDist->classCount()}</p>";
                }
            }

            return $reply;
        }else{
            return "No time available";
        }
    }

    /**
     * Displays a single CsClassScheduleDetail model.
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
        $model = new CsClassScheduleDetail();
        // schedDistId,coffdetId,type,room
        // var_dump($_POST);die();
        if (isset($_POST['schedDistId']) && !empty($_POST['schedDistId'])) {
            $model->class_time_distrib_relId = intval($_POST['schedDistId']);
            $model->coff2det_relId = intval($_POST['coffdetId']);
            $model->gset_relId = intval($_POST['group']);

            $model->class_type = $_POST['type'];
            $model->room_relId = intval($_POST['room']);

            $exists = $model->getSchedule($model->coff2det_relId,$model->class_time_distrib_relId,$model->room_relId);

            if ($exists && count($exists) > 0) {
                unset($model);
                return 'The selected class time is already held. Please choose another';
            }else{
                $model->created_by = Yii::$app->user->identity->id;
                $model->created_at = time();

                $model->data = $model->getData();

                if ($model->save()) {
                    return 1;
                }else{
                    return json_encode($model->getErrors());
                }
            }
        }else{
            return "InsuFficient data ProVided, Please corect your choice and try again.";
        }
    }

    /**
     * Creates a new CsClassScheduleDetail model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new CsClassScheduleDetail();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        }

        return $this->render('create', [
            'model' => $model,
        ]);
    }

    /**
     * Updates an existing CsClassScheduleDetail model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     * @throws NotFoundHttpException if the model cannot be found
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post())) {

            $model->updated_by = Yii::$app->user->identity->id;
            $model->updated_at = time();
            $model->data = $model->getData();


            if ($model->save()) {
                if (Yii::$app->request->isAjax) {
                    return 1;
                }else{
                    return $this->redirect(['/time-schedule/index']);
                }
            }else{
                if (Yii::$app->request->isAjax) {
                    return json_encode($model->getErrors());
                }else{
                    // Yii::$app->session->setFlash('type','error');
                    Yii::$app->session->setFlash('error',json_encode($model->getErrors()));
                }
            }
        }
        if (Yii::$app->request->isAjax) {
            return $this->renderPartial('update', [
                'model' => $model,
            ]);
        }else{
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing CsClassScheduleDetail model.
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
     * Finds the CsClassScheduleDetail model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return CsClassScheduleDetail the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = CsClassScheduleDetail::findOne($id)) !== null) {
            return $model;
        }

        throw new NotFoundHttpException(Yii::t('rms', 'The requested page does not exist.'));
    }
}
