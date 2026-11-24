<?php

namespace frontend\controllers;
use backend\models\WGroupSetup;
use  \frontend\models\SemesterRegistration2Detail;
use \common\components\CommonControllerFunctions AS CCF;

class AcademicDefficiencyController extends \yii\web\Controller
{
    public function actionIndex()
    {
    	$model = new \backend\models\StudentSemesterStatusHistory();
        return $this->render('index',['model'=>$model]);
    }

    public function actionGetAdStudentList(){
    	$acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId'])?intval($_POST['acadYearId']):die('acadYearId Error..');
        $semId = isset($_POST['semId']) && !empty($_POST['semId'])?intval($_POST['semId']):die('semId Error..');
        $admId = isset($_POST['admId']) && !empty($_POST['admId'])?intval($_POST['admId']):die('Uknown Admission');
        $progId = isset($_POST['progId']) && !empty($_POST['progId'])?intval($_POST['progId']):die('Uknown Program');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])?intval($_POST['clslvlId']):die('Uknown Class Year.');

        // var_dump($clslvlId);die();

        return $this->renderPartial('_report/ad-list-report',
        	[
        		'acadYearId' => $acadYearId,
        		'semId' => $semId,
        		'admId' => $admId,
        		'progId' => $progId,
        		'clslvlId' => $clslvlId,
        	]);
    }

    private function getSemGrade($stdId,$clslvl,$sem,$cGset,$program = NULL){
    	$semObject = \backend\models\Semester::findOne(['semester_name'=>$sem]);
    	$clslvlObj = \backend\models\ClassLevel::findOne(['clslvl_num'=>$clslvl,'clslvl_program_relId'=>$program]);
    	$stdObject = \backend\models\Student::findOne($stdId);

    	$semregObj = new SemesterRegistration2Detail();
    	$semReport = $semregObj->getSemregRecord($stdId,$clslvlObj->id,$semObject->id,$cGset);

    	if (is_array( $semReport )) {
    		$semReport[0] = is_numeric($semReport[0])?sprintf("%0.2f",$semReport[0]):$semReport[0];
    		$semReport[1] = is_numeric($semReport[1])?sprintf("%0.2f",$semReport[1]):$semReport[1];
    		return $semReport;
    	}
    	return [NULL,NULL,NULL];
    }

}
