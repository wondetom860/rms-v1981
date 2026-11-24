<?php

namespace frontend\controllers;
use \backend\models\CourseCordinator;
use \common\components\CommonControllerFunctions as CCF;

class AssignSemesterCourseCordinatorController extends \yii\web\Controller
{
	public $acadYearId;
    public $semId;
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    public function actionIndex(){
    	if (\Yii::$app->user->can('DepartmentHead')) {
    		return $this->render('index');
    	}
    }

    public function actionRemoveCordinator(){//remove-cordinator RemoveCordinator
    	if (\Yii::$app->user->can('DepartmentHead')) {
    		$id = isset($_POST['id']) && !empty($_POST['id'])?intval($_POST['id']):die('Uknown Entry..');
    		$result = CourseCordinator::findOne($id)->delete();
    		echo "{$result}";die();
    	}
    }

    public function actionSetCordinator(){ //

    	$courseId = isset($_POST['courseId']) && !empty($_POST['courseId'])?intval($_POST['courseId']):die('Uknown Course.');
    	$instId = isset($_POST['instId']) && !empty($_POST['instId'])?intval($_POST['instId']):die('Uknown Instructor.');
    	$admId = isset($_POST['admId']) && !empty($_POST['admId'])?intval($_POST['admId']):die('Uknown Entry.');
    	if (\Yii::$app->user->can('DepartmentHead')) {
    		$model = new CourseCordinator();
    		$this->__constructor();

    		$exists = $model->checkIfExists($courseId);

    		if ($exists && count($exists) > 0 ) {
    			echo "Already Assigned.";
    		}else{
    			$model->cc_course_relId = $courseId;
    			$model->cc_adm_relId = $admId;
    			$model->cc_inst_relId  =$instId;

    			$model->cc_acadYear_relId = $this->acadYearId;
    			$model->cc_sem_relId = $this->semId;
    			$model->cc_created_at  =date('Y-m-d h:i:s');
    			$model->cc_created_by  =\Yii::$app->user->identity->id;

    			echo $model->save();
    		}
    	}
    	die();
    }
    public function actionGetMinstCourse(){
    	// list those courses assigned for multiple instructors.
    	$coffdetObject = new \backend\models\CourseOffering2Detail();

    	$mInstdCourses  = $coffdetObject->getmInstedCourses();

    	if ($mInstdCourses && count($mInstdCourses) > 0) {
    		echo "<table class ='table table-striped table-bordered table-hover table-condensed table-sm' style='font-size:9pt;'>";
    		echo "<caption>
    				<p>Your Department Courses Assigned to multiple instructors</p>
    			</caption>";
    		echo "<thead style='background-color:#8D0;'>
    				<th style='border:1px solid black'>#</th>
    				<th style='border:1px solid black'>Course Code</th>
    				<th style='border:1px solid black'>Course Title</th>
    				<th style='border:1px solid black'>Course Assignment Info.</th>
    				<th style='border:1px solid black'>Cordinator</th>
    		</thead><tbody>";
    		$count = 0;
    		foreach ($mInstdCourses as $key) {
    			$course = \backend\models\Course::findOne($key->coff2det_course_relId);
    			$offeringDet = $coffdetObject->getDepts($key->coff2det_course_relId);
    			echo "<tr>
    				<td style='vertical-align:middle; border:1px solid black; text-align:center;'>".++$count."</td>
    				<td style='vertical-align:middle; border:1px solid black; text-align:center;'>{$course->course_code}</td>
    				<td style='vertical-align:middle; border:1px solid black;'>{$course->course_title}</td>
    				<td style='vertical-align:middle; border:1px solid black;'>{$offeringDet['ps']}</td>
    				<td style='vertical-align:middle; border:1px solid black;'>{$offeringDet['seles']}</td>
    			</tr>";
    		}
    		echo "</tbody></table>";
    	}else{
    		echo "No Multiple Instructor assigned course found.";
    	}

    	die();
    }

}
