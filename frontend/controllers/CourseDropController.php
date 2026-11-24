<?php

namespace frontend\controllers;
use yii;
use backend\models\Course;
use frontend\models\SemesterRegistration2Detail;
use common\components\CommonControllerFunctions as CCF;

class CourseDropController extends \yii\web\Controller
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
        return $this->render('index');
    }

    public function actionRequestcoursedrop(){
    	if (isset($_POST['courseId']) && !empty($_POST['courseId']) 
			&& isset($_POST['srd2Id']) && !empty($_POST['srd2Id'])) {
			// srd2Id
    		$courseId = $_POST['courseId'];
    		$srd2 = $_POST['srd2Id'];

    		$this->__constructor();
    		$std = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);
    		$model = new \backend\models\CourseDropHistory();
    		$model->cdh_std_relId = $std->id;
    		$model->cdh_acadYear_relId = $this->acadYearId;
    		$model->cdh_sem_relId = $this->semId;
    		$model->cdh_clslvl_relId = $std->std_clslvl_relId;
    		$model->cdh_group_relId = NULL;//assign student group assignment ID: 
    		$model->cdh_srd2_relId = $srd2; 						//Check this later
    		$model->cdh_course_relId = $courseId;
    		$model->cdh_created_at = date('Y-m-d h:i:s');

    		if($model->save()){
    			echo 1;
    		}else{
    			print_r($model->errors);die();
    		}
    	}else{
    		echo "No data provided.";
    	}
    }

    public function actionRequeststatus(){
    	$this->__constructor();
    	$std = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);
    	$requstedCourses = \backend\models\CourseDropHistory::find()
		    	->where([
		    		'cdh_std_relId'=>$std->id,
		    		'cdh_acadYear_relId'=>$this->acadYearId,
		    		'cdh_sem_relId'=>$this->semId,
		    	])
		    	->all();

		  if ($requstedCourses) {

		  	echo "<table style='font-size:10pt;' class='table table-striped table-condensed table-sm table-bordered'>";
		    echo "<caption><p class='bg-success'> <i class='bg-danger'>". count($requstedCourses) ."</i>Request(s).</p></caption>";
		    echo "<thead>
		    <th>Course Title</th>
		    <th>Status</th>
		    <th>Date</th>
		    </thead><tbody>";
			foreach ($requstedCourses as $course) {

			    $c = \backend\models\Course::findOne(['id'=>$course->cdh_course_relId]);
			    $status = is_null($course->cdh_status)?'Pending':'Approved';
			    echo "<tr>
			    <td>". $c->course_title ."</td>
			    <td>".$status."</td>
			    <td>".date_format(date_create($course->cdh_created_at),'D, M d, Y')."</td>
			    </tr>";
			}
			echo "</tbody></table>";

		  }else{
		  	echo "<p class='bg-danger'>No request made yet.</p>";
		  }
    }

    protected function currentStudentGroup($stdObject){
      $this->__constructor();
      $gset = \backend\models\WGroupSetup::find()
        ->where([
          'gs_acadYear_relId'=>$this->acadYearId,
          'gs_sem_relId'=>$this->semId,
          'gs_adm_relId'=>$stdObject->std_admission_relId,
          'gs_prog_relId'=>$stdObject->std_prog_relId,
        ])
        ->innerJoin('w_group_assignment','w_group_setup.id = w_group_assignment.gas_gset_relId')
        ->andWhere(['w_group_assignment.gas_std_relId'=>$stdObject->id])
            ->with('wGroupAssignments')
        ->one();
      return $gset;
    }

    private function GCStudent($gset){
      if (strtoupper($gset->gsProgRel->program_name) == 'MASTERS') {
        return $gset->gsClslvlRel->clslvl_num * 2 == 4;
      }else if(strtoupper($gset->gsProgRel->program_name) == 'DEGREE'){
        return $gset->gsClslvlRel->clslvl_num * 2 == 10;
      }else{
        return false;
      }
    }


    private function getValidGrades($stdObj,$gsetObj){
    	$sgrades = null;
    	$validGrades = [];
	    if (isset( $stdObj->std_prog_relId)) {
	        $program = \backend\models\Program::findOne($stdObj->std_prog_relId);
	        if (strtoupper($program->program_name) == 'MASTERS') {
	          $sgrades = \backend\models\GradeScale::find()->where([
	              'gs_ltr_grade'=>['A','A+','B','B+']
	            ])->all();
	        }else{
	          //check if graduating classs
	          	if ($this->GCStudent($gsetObj)) {//GC students can add D courses
	            	$sgrades = \backend\models\GradeScale::find()->where([
	              	'gs_ltr_grade'=>['C','C+','A','A+','B','B+']
	            	])->all();
	          	}else{
	            	$sgrades = \backend\models\GradeScale::find()->where([
	              	'gs_ltr_grade'=>['C','C+','D','A','A+','B','B+']
	            	])->all();
          		}
    		}

			if ($sgrades && count($sgrades) > 0) {
			    foreach ($sgrades as $key) {
			      $validGrades[] = $key->id;
			    }
		  	}
      	}
      	return $validGrades;
    }

    public function actionGetenrolledcourses(){
    	$this->__constructor();
    	$std = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);

    	$gset = $this->currentStudentGroup($std);
    	// check if course-drop schedule is open.
    	$actionName = 'Course drop';
	    $action = \backend\models\AcademicCalanderAction::findOne(['aca_name'=>$actionName]);
	    //var_dump($action->id);die();
	    if ($action) {
	      $schedule = CCF::getScheduleForAction($gset,$action->id);
	      if (!($schedule && count($schedule))) {
	        die('Schedule is not open for course drop.');
	      }
	    }else{
	      die('Unregistered action {$actionName}');
	    }

	    $validGrades = $this->getValidGrades($std,$gset);
	    // echo "<pre>";
	    // var_dump($validGrades);
	    // die();
    	//getStudentGroupHistory($clslvlId,$semesterId,$studentId)
    	//$enrolledCourses2 = CCF::getStudentGroupCoursesP($gsetId,$studentId,$semesterId,$clslvlId)
    	// 
    	$enrolledCourses = SemesterRegistration2Detail::find()
	    ->innerJoinWith('srd2SemregRel','semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
	    ->andWhere(['semester_registration_2.semreg_acadYear_relId'=>$this->acadYearId])
	    ->andWhere(['semester_registration_2.semreg_semester_relId'=>$this->semId])
	    ->andWhere(['semester_registration_2.semreg_std_relId'=>$std->id])
	    ->andWhere(['OR',['NOT IN','srd2_grade_relId',$validGrades],['srd2_grade_relId'=>NULL]])
	    ->all();



	    if ($enrolledCourses) {
	    	$modelCourseDropObj = new \backend\models\CourseDropHistory();
		    $modelCourseDropObj->dropPossible($std);

	    	echo "<table class='table table-striped table-condensed table-sm table-bordered' style='font-size:9pt;'>";
		    echo "
		    		<caption>
		    			<p class='bg-success'> 
		    				<i class='bg-danger'>". count($enrolledCourses) ."</i> Course(s).
		    			</p>
		    			<small>
		    				Creadits Collected In This Semester: <b><i>{$modelCourseDropObj->collectedCredit}</i></b>, 
				            Credit Hour to Enroll: [<b><i>{$modelCourseDropObj->minCredit}-{$modelCourseDropObj->maxCredit}</i></b>],
				            Approval Pending drop Credits: <b><i>{$modelCourseDropObj->pendingCredit}</i></b>,
				            Free Credit Hrs: <b><i>{$modelCourseDropObj->dropableCreditHrs}</i></b>,
		    			</small>
		    		</caption>";
		    echo "<thead>
		    <th>Course Title</th>
		    <th>Credit Hrs.</th>
		    <th>Action</th>
		    </thead><tbody>";
			foreach ($enrolledCourses as $course) {

			    $c = Course::findOne(['id'=>$course->srd2_course_relId]);
			    $requstedCourses = \backend\models\CourseDropHistory::find()
			    	->where([
			    		'cdh_std_relId'=>$std->id,
			    		'cdh_acadYear_relId'=>$this->acadYearId,
			    		'cdh_sem_relId'=>$this->semId,
			    		'cdh_course_relId'=>$c->id,
			    		'cdh_status'=>NULL,
			    	])
			    	->all();
			    	if ($requstedCourses) {
			    		continue;
			    	}
			    	$btn = "";
			    	if ($modelCourseDropObj->dropableCreditHrs > 0 && $c->course_crdthr <= $modelCourseDropObj->dropableCreditHrs) {
			    		$btn = "<button class='btn btn-xs btn-warning' onclick='req(". $c->id .",". $course->id.")' title='Request course drop for this course.'>Drop.</button>";
			    	}else{
			    		$btn = "<small>Can NOT be droped, Below Minimum required credit hour.</small>";
			    	}
			    	// [{$c->course_code}] - 
			    echo "<tr>
				    <td>{$c->course_title}</td>
				    <td>{$c->getCourseCredits()}</td>
				    <td>{$btn}</td>
				    </tr>";
			}
			echo "</tbody></table>";
	    }else{
	    	echo "<p class='bg-danger'>No course to drop.</p>";
	    }
    }
}
