<?php

namespace frontend\controllers;
use yii;
use yii\web\Controller;
use \common\components\CommonControllerFunctions as CCF;
use \frontend\modules\ClassSchedule\models\CsClassScheduleDetail;

class ClassScheduleReportController extends Controller
{

	public $acadYearId;
    public $semId;
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    public function actionIndex()
    {
    	$models = null;
    	$model = new CsClassScheduleDetail();
    	$coffs = null;
    	$gset = null;
    	$type = null;

    	if (Yii::$app->user->can('Student')) {
    		// get report by gset
    		// ?get the group where the student is assigned in -> and then the schedule setup
    		$studentObject = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);

    		$gset = CCF::currentStudentGroup($studentObject);
    		$type = 1;

    	}else if (Yii::$app->user->can('Instructor')) {
    		// get report by coff
    		$type = 2;
    		$instructor = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
    		$this->__constructor();
    		// get all courses offering made for an instructor in this semester.
    		$coffs = $instructor->getCourseOffering2Details($this->acadYearId,$this->semId);
    	}

    	// $models = $model->scheduleReport($coffs,$gset);
    	$coffdetModel = ($coffs && count($coffs) > 0) ? $coffs[0] : null;
    	return $this->render('index',['model' => $model,'type' => $type,'coffdetModel' => $coffdetModel,'gsetModel' => $gset]);
        // return $this->render('index',['models' => $models]);
    }

}
