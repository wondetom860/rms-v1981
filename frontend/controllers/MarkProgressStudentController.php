<?php

namespace frontend\controllers;
use yii;
use yii\data\ActiveDataProvider;
use \common\components\CommonControllerFunctions as CCF;

class MarkProgressController extends \yii\web\Controller
{
	public $acadYearId;
    public $semId;
    public $pieReportArray = [];
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGetMarkProgressReport($d = NULL){

    	$contAssess = $this->continousAssessmentReport();
  //   	echo "<pre>";
		// var_dump($contAssess);die();
		$courseArray = [];

    	if ($contAssess && count($contAssess) > 0) {
    		foreach ($contAssess as $key) {
    			$coffDet = \backend\models\CourseOffering2Detail::findOne($key->ca_coff_relId);

    			if (!in_array($coffDet->coff2det_course_relId, $courseArray)) {
    				$courseArray[]  = $coffDet->coff2det_course_relId;
    				if(is_null($d)){
						echo $this->courseAssessmentProgressDetail($key,$coffDet->coff2det_course_relId,$d);
    				}else{
    					$this->courseAssessmentProgressDetail($key,$coffDet->coff2det_course_relId,$d);
    				}
    			}    			
    		}if (!is_null($d)) {
    			return $this->pieReportArray;
    		}die();
    	}else{
    		echo "<p class='bg-danger'>No record found.<p>";die();
    	}
    	
    }

    private function continousAssessmentReport(){
    	if (Yii::$app->user->can('Student')) {
    		$std = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);
    		$this->__constructor();
    		$contAssess = \backend\models\ContinuousAssessment::find()
	    		->innerJoin('course_offering2_detail','continuous_assessment.ca_coff_relId = course_offering2_detail.id')
	    		->innerJoin('semester_registration_2','semester_registration_2.id = continuous_assessment.ca_semreg_relId')
	    		->andWhere([
	    			'semester_registration_2.semreg_acadYear_relId'=>$this->acadYearId,
	    			'semreg_sem_relId'=>$this->semId
	    		])
	    		->where(['ca_std_relId'=>$std->id])
	    		->indexBy('ca_coff_relId')
	    		->all();

	    	return $contAssess;
    	}

    	return NULL;
    }

    public function actionPie()	{
    	$qq = \backend\models\ContinuousAssessment::find()->groupBy('ca_semreg_relId');
    	//$qq = array_merge($this->pieReportArray);
		$dataProvider = new ActiveDataProvider([
			'query' => $qq,
		    'pagination' => false
		]);

		$qq = $qq->all();
		
		return $this->render('pie', [
			'dataProvider' => $dataProvider,
			'model'=>$qq,
		]);
	}

	public function actionChart()	{
		if (empty($this->pieReportArray)) {
			$this->actionGetMarkProgressReport(1);
		}
    	return $this->renderAjax('chart',['courses'=>$this->pieReportArray]);
	}

    private function courseAssessmentProgressDetail($contAssessObject,$courseId,$d=NULL){
    	if (!(is_null($contAssessObject))) {
    		$course = \backend\models\Course::findOne($courseId);
    		$contAssess = \backend\models\ContinuousAssessment::find()
    			->where([
    				'ca_coff_relId'=>$contAssessObject->ca_coff_relId,
    				'ca_semreg_relId'=>$contAssessObject->ca_semreg_relId,
    			])
    			->all();
    			$totalAssigned = 0;
    			$totalObtained = 0;
    		if ($contAssess && count($contAssess) > 0) {
    			foreach ($contAssess as $key) {

    				// $catagory = \backend\models\ContAssessCatagory::findOne($key->)
    				$totalObtained += $key->ca_markObtained;
    				$assignment = \backend\models\ContAssessMarkAssignment::findOne($key->ca_assignment_relId);
    				$totalAssigned += $assignment->ma_possiblePoint;
    			}
    		}

    	$this->pieReportArray[] = [
    		"Course_Title"=>$course->course_title,
    		'Mark'=>round(($totalObtained/$totalAssigned),2,PHP_ROUND_HALF_UP) *100,
    		'title'=>"{$totalObtained}/{$totalAssigned}",
    		'course_code'=>$course->course_code,
    	];
    	if (is_null($d)) {
    		return "<p>{$course->course_title} => {$totalObtained}/{$totalAssigned}</p>";
    	}else{
    		return $this->pieReportArray;
    	}
    	}else{
    		return '-';
    	}
    }
}
