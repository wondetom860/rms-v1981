<?php
namespace common\components;
use yii;
use \backend\models\AcademicCalander;
use \common\components\CommonControllerFunctions as CCF;


/**
 * 
 */
class Admission
{
	public function ScheduleOpen(){
		$model = new AcademicCalander();
		// get academic program schedule(s) with active semester registration activity set on.
        $modelAps = new \frontend\modules\AcademicCalendar\models\AcProgramSchedule();
        return $modelAps->getScheduledProgramsforActivity('Academic Admission');

		// return $model->checkIfOpen('_Academic%5BAdmission');
	}
}
?>