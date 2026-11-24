<?php
namespace frontend\controllers;
use yii;
use backend\models\Course;
use frontend\models\SemesterRegistration2;
use frontend\models\SemesterRegistration2Detail;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use \common\components\CommonControllerFunctions as CCF;

class MakeupExamRequestController extends \yii\web\Controller
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

    // Approve or di-approve the request by registrar head.
    public function actionChangerequeststatus(){

        $actionId = isset($_POST['actionId']) && !empty($_POST['actionId'])?intval($_POST['actionId']):die('Uknown Action');
        $reqId = isset($_POST['reqId']) && !empty($_POST['reqId'])?intval($_POST['reqId']):die('Uknown Request');

        $actionId = $actionId == 1?$actionId:0;

        $request = NULL;

        if (Yii::$app->user->can('DepartmentHead')) {
        	$request  = \backend\models\MakeupExamRequest::find()
        	->where([
	        	'id'=>$reqId,
	        	'mer_approved'=>NULL,
	        ])
	        ->one();
        }else if (Yii::$app->user->can('Registrar')) {
        	$request  = \backend\models\MakeupExamRequest::find()->where([
	        	'id'=>$reqId,
	        	'mer_approved'=>1,
	        ])->andWhere(['mer_registrarApprove_status'=>NULL])->one();
        }
        $this->__constructor();

        if ($request) {
        	$transaction = Yii::$app->db->beginTransaction();

        	try{
        		$result = NULL;
	        	if (Yii::$app->user->can('DepartmentHead')) {
	        		$result = Yii::$app->db->createCommand()
	    			->update('makeup_exam_request',//add_history
	                    [
	                        'mer_approved'=>$actionId,
	                        'mer_approved_by'=>Yii::$app->user->identity->id,
	                        'mer_updated_at'=>date('Y-m-d h:i:s'),
	                        
	                     ],
	                    'id='.$reqId)  
	    			->execute(); 
	        	}else if (Yii::$app->user->can('Registrar')) {
	        		$result = Yii::$app->db->createCommand()
	    			->update('makeup_exam_request',//add_history
	                    [
	                        'mer_registrarApprove_status'=>$actionId,
	                        'mer_approved_by'=>Yii::$app->user->identity->id,
	                        'mer_updated_at'=>date('Y-m-d h:i:s'),
	                        
	                     ],
	                    'id='.$reqId)  
	    			->execute(); 
	        	}else{
	        		// "<p class='bg-danger'></p>";
	        		throw new \ErrorException("You have no privilage for this operation.", 1);
	        	}
	        	if ($result) {
	        		if ($actionId == 0) { //rejected requests, 
	        			// change requested course grade to F automatically.
	        			$this->changeGradeToF($request);
	        			$transaction->commit();
	        			echo 1;
	        		}else{
	        			$transaction->commit();
	        			echo 1;
	        		}
		 		}else{
		 			throw new \ErrorException("Failed to Approve the request.", 1);	
		 		}
        	}catch(\Exception $e){
        		$transaction->rollBack();
        		echo "<p class='bg-danger'>ERROR: {$e->getMessage()}</p>";
        		die();
        	}
        }else{
        	echo "No such add request found!";die();
        }

        // echo json_encode(['action Id: '=>$actionId,'Req. Id: '=>$reqId]);die();
    }

    // Change requested courses' grade to F as the request is rejected.
    private function changeGradeToF($requestObject){
    	if (!is_null($requestObject)) {
    		$fGrade = \backend\models\GradeScale::findOne(['gs_ltr_grade'=>'F']);
    		if ($fGrade) {
    			$rrt = Yii::$app->db->createCommand()
	    			->update('semester_registration_2_detail',
	    				['srd2_grade_relId'=>$fGrade->id],
	    				['id'=>$requestObject->mer_srd2_relId])
	    			->execute();
	    		if ($rrt) {
	    			// return TRUE;
	    		}else{
	    			throw new \ErrorException("Failed to Make student grade back to F. Please retry again.", 1);
	    		}
    		}else{
    			throw new \ErrorException("F Grade is not Available in the grade Scale list.", 1);
    		}
    	}else{
    		throw new \ErrorException("Invalid request, REQUEST EMPTY.", 1);
    	}
    }

    // Get current group of the student.
    protected function getCurrentStudentGroup($stdId){
    	$this->__constructor();
    	$gset = \backend\models\WGroupSetup::find()
    		->where([
    			'gs_acadYear_relId'=>$this->acadYearId,
    			'gs_sem_relId'=>$this->semId,
    		])
    		->innerJoin('w_group_assignment','w_group_assignment.gas_gset_relId = w_group_setup.id')
    		->andWhere(['gas_std_relId'=>$stdId])
    		->one();

    	return $gset;
    }

    // Save the request made by student
    public function actionRequestmakeupexam(){
    	if (isset($_POST['srd2Id']) && !empty($_POST['srd2Id']) && isset($_POST['reasonId']) && !empty($_POST['reasonId']) && isset($_POST['courseId']) && !empty($_POST['courseId'])) {

    		$reasonId = $_POST['reasonId'];
    	    $srd2 = $_POST['srd2Id'];
    	    $courseId = $_POST['courseId'];

    		$this->__constructor();
    		if (Yii::$app->user->can('Student')) {
    			$std = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);

    			$gset = $this->getCurrentStudentGroup($std->id);

    			if (!($gset)) {
    				die('Group assignment Error.');
    			}

	    		$model = new \backend\models\MakeupExamRequest();
	    		$model->mer_std_relId = $std->id;
	    		$model->mer_acadYear_relId = $this->acadYearId;
	    		$model->mer_sem_relId = $this->semId;
	    		$model->mer_clslvl_relId = $std->std_clslvl_relId;
	    		$model->mer_group_relId = $gset->id; 
	    		$model->mer_srd2_relId = $srd2; 						//Check this later
	    	    $model->mer_reason_relId = $reasonId;
	    	    $model->mer_course_relId = $courseId;
	    	     $model->mer_updated_by = Yii::$app->user->identity->id;
	    		$model->mer_created_at = $model->mer_updated_at = date('Y-m-d h:i:s');

	    		if($model->save()){
	    			echo 1;
	    		}else{
	    			print_r($model->errors);die();
	    		}
    		}else{
    			die('Not Student');
    		}
    	}else{
    		echo "No data provided.";
    	}
    }

    // show request status back for student pending , approved, rejected.
    public function actionRequeststatus(){
    	$this->__constructor();

    	// Show request status for requested student.
    	if (Yii::$app->user->can('Student')) {
    		
	    	$std = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);
	    	$requstedCourses = \backend\models\MakeupExamRequest::find()
			    	->where([
			    		'mer_std_relId'=>$std->id,
			    		'mer_acadYear_relId'=>$this->acadYearId,
			    		'mer_sem_relId'=>$this->semId,
			    	])
			    	->all();

			  if ($requstedCourses) {

			  	echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
			    echo "<caption><p class='bg-success'> <i class='bg-danger'>". count($requstedCourses) ."</i> Course(s).</p></caption>";
			    echo "<thead>
			    <th>Course Title</th>
			    <th>Reason</th>
			    <th>Status</th>
			    </thead><tbody>";
				foreach ($requstedCourses as $course) {

					$reason = \backend\models\MakeupExamReason::findOne(['id'=>$course->mer_reason_relId])->mer_desc;
				    $c = \backend\models\Course::findOne(['id'=>$course->mer_course_relId]);
			
				    $status = NULL;
				    if (is_null($course->mer_approved)) {
				    	$status = 'Waiting for HoD approval';
				    }else{
				    	if (is_null($course->mer_registrarApprove_status)) {
				    		$status = 'Waiting for registrar approval';
				    	}else{
				    		if ($course->mer_approved == 0 || $course->mer_registrarApprove_status == 0) {
				    			$status = 'Request rejected.';
				    		}else if ($course->mer_approved == 1 && $course->mer_registrarApprove_status == 1) {
				    			$status = 'Approved';
				    		}
				    	}
				    }
				    echo "<tr>
				    <td>". $c->course_title ."</td>
				    <td>{$reason}</td>
				    <td>".$status."</td>
				    </tr>";
				}
				echo "</tbody></table>";

			}else{
			  	echo "<p class='bg-danger'>No request made yet.</p>";
			}
    	}else if (Yii::$app->user->can('Registrar')) {
    		$requstedCourses = \backend\models\MakeupExamRequest::find()
		    	->where([
		    		'mer_acadYear_relId'=>$this->acadYearId,
		    		'mer_sem_relId'=>$this->semId,])
		    	->andWhere(['IS NOT','mer_approved',NULL])
		    	->andWhere(['IS NOT','mer_registrarApprove_status',NULL])
		    	->all();

			if ($requstedCourses) {

			  	echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
			    echo "<caption><p class='bg-success'> <i class='bg-danger'>". count($requstedCourses) ."</i> Course(s).</p></caption>";
			    echo "<thead>
			    <th>Reg. ID</th>
			    <th>Full Name</th>
			    <th>Course Title</th>
			    <th>Reason</th>
			    <th>HoD Appr. Status</th>
			    <th>Registrar Apprv. Status</th>
			    </thead><tbody>";
				foreach ($requstedCourses as $course) {
					$std = \backend\models\Student::findOne(['id'=>$course['mer_std_relId']]);
					$user = \backend\models\User::findOne(['id'=>$std->std_user_relId]);
					$reason = \backend\models\MakeupExamReason::findOne(['id'=>$course->mer_reason_relId])->mer_desc;
				    $c = \backend\models\Course::findOne(['id'=>$course->mer_course_relId]);
			
				    $status = is_null($course->mer_approved)?'Pending':($course->mer_approved == 1?'Accepted':'Dis-approved');
				    $status2 = is_null($course->mer_registrarApprove_status)?'Pending':($course->mer_registrarApprove_status == 1?'Accepted':'Dis-approved');
				    echo "<tr>
				    <td>{$std->std_regId}</td>
				    <td>{$user->first_name} {$user->middle_name}</td>
				    <td>". $c->course_title ."</td>
				    <td>{$reason}</td>
				    <td> {$status}
				    <td> {$status2}
				    </td>
				    </tr>";
				}
				echo "</tbody></table>";

			}else{
				echo "<p class='bg-danger'>No request made yet.</p>";
			}
    	}else if (Yii::$app->user->can('DepartmentHead')) {
    		// ->andWhere(['IS NOT','mer_registrarApprove_status',NULL])

    		$instDept = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id])->inst_dep_relId;

    		$requstedCourses = \backend\models\MakeupExamRequest::find()
			    	->where([
			    		'mer_acadYear_relId'=>$this->acadYearId,
			    		'mer_sem_relId'=>$this->semId,
			    	])
			    	->innerJoin('course','course.id = makeup_exam_request.mer_course_relId')
			    	->andWhere(['course.course_dept_relId'=>$instDept])
			    	->andWhere(['IS NOT','mer_approved',NULL])
			    	->all();

			if ($requstedCourses) {

			  	echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
			    echo "<caption><p class='bg-success'> <i class='bg-danger'>". count($requstedCourses) ."</i> Course(s).</p></caption>";
			    echo "<thead>
			    <th>Reg. ID</th>
			    <th>Full Name</th>
			    <th>Course Title</th>
			    <th>Reason</th>
			    <th>HoD Appr. Status</th>
			    <th>Registrar Apprv. Status</th>
			    </thead><tbody>";
				foreach ($requstedCourses as $course) {
					$std = \backend\models\Student::findOne(['id'=>$course['mer_std_relId']]);
					$user = \backend\models\User::findOne(['id'=>$std->std_user_relId]);
					$reason = \backend\models\MakeupExamReason::findOne(['id'=>$course->mer_reason_relId])->mer_desc;
				    $c = \backend\models\Course::findOne(['id'=>$course->mer_course_relId]);
			
				    $status = is_null($course->mer_approved)?'Pending':($course->mer_approved == 1?'Accepted':'Dis-approved');
				    $status2 = is_null($course->mer_registrarApprove_status)?'Pending':($course->mer_approved == 1?'Accepted':'Dis-approved');
				    echo "<tr>
				    <td>{$std->std_regId}</td>
				    <td>{$user->first_name} {$user->middle_name}</td>
				    <td>". $c->course_title ."</td>
				    <td>{$reason}</td>
				    <td> {$status}
				    <td> {$status2}
				    </td>
				    </tr>";
				}
				echo "</tbody></table>";

			}else{
				echo "<p class='bg-danger'>No request made yet.</p>";
			}
    	}

    }

    // Loads the enrolled course for make exam request =>Student

    // Load requsets made by students for Registrar Head.
    public function actionGetenrolledcourses(){
    	$this->__constructor();
    	if (Yii::$app->user->can('Student')) {
    		$std = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);

    		$gset = $this->getCurrentStudentGroup($std->id);
	    	if (!($gset)) {
	    		die('Group setup not made yet, please come later.');
	    	}

	    	$actionName = 'Makeup Examination';
		    $action = \backend\models\AcademicCalanderAction::findOne(['aca_name'=>$actionName]);
		    if ($action) {
		      $schedule = CCF::getScheduleForAction($gset,$action->id);
		      if (!($schedule)) {
		        die('Schedule is not open for make-up examination');
		      }
		    }else{
		      die('Unregistered action {$actionName}');
		    }

		    $iGrade = \backend\models\GradeScale::findOne(['gs_ltr_grade'=>'I'])->id;

		    // exclude already requsested couses
		     $requstedCourses = \backend\models\MakeupExamRequest::find()
			    	->where([
			    		'mer_std_relId'=>$std->id,
			    		'mer_acadYear_relId'=>$this->acadYearId,
			    		'mer_sem_relId'=>$this->semId,
			    	])
			    	->all();
			    $requestedCoursesArray  = [];
	    	if ($requstedCourses) {
	    		foreach ($requstedCourses as $key) {
	    			$requestedCoursesArray[] = $key['mer_course_relId'];
	    		}
	    	}

	    	$enrolledCourses = SemesterRegistration2Detail::find()
		    ->innerJoinWith('srd2SemregRel','semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
		    ->andWhere(['semester_registration_2.semreg_acadYear_relId'=>$this->acadYearId])
		    ->andWhere(['semester_registration_2.semreg_semester_relId'=>$this->semId])
		    ->andWhere(['semester_registration_2.semreg_std_relId'=>$std->id])
		    ->andWhere(['NOT IN','semester_registration_2_detail.srd2_course_relId',$requestedCoursesArray])
		    ->andWhere(['semester_registration_2_detail.srd2_grade_relId'=>$iGrade])
		    ->all();



		    if ($enrolledCourses) {
		    	echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
			    echo "<caption><p class='bg-success'> <i class='bg-danger'>". count($enrolledCourses) ."</i> Course(s).</p></caption>";
			    echo "<thead style='background-color:#ace;'>
			    <th>Course Title</th>
			    <th>Reason</th>
			    <th>Option</th>
			    </thead><tbody>";
			    $model = new \backend\models\MakeupExamRequest();

			    ?>
			    <?php $form = ActiveForm::begin(); ?>

			    <?php

			foreach ($enrolledCourses as $course) {

			    $c = \backend\models\Course::findOne(['id'=>$course->srd2_course_relId]);
			
			    echo "<tr>
			    <td>". $c->course_title ."</td>
				<td>";
				?>
				<?= $form->field($model, 'mer_reason_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\MakeupExamReason::find()->all(), 'id', 'mer_desc'),
                            [
                                'prompt'=>'Select reason',
                                'required'=>TRUE,
                                
                            ]
                        )->label('') ?>
				<?php
				echo "</td>
			    <td><button class='btn btn-sm btn-success' onclick='req(".  $course->id.",".$course->srd2_course_relId.")' title='Request Makeup Exam for this course.'>Req.</button></td>
			    </tr>";
			}
			?>
			<?php ActiveForm::end(); ?>

			<?php
			echo "</tbody></table>";
		    }else{
		    	echo "<p class='bg-danger'>No I marked course found. Please contact HoD.</p>";
		    }
    	}else{
			$requstedCourses =NULL;
			if(Yii::$app->user->can('Registrar')){
    		$requstedCourses = \backend\models\MakeupExamRequest::find()
			    	->where([
			    		'mer_acadYear_relId'=>$this->acadYearId,
			    		'mer_sem_relId'=>$this->semId,
			    	])
			    	->andWhere(['mer_approved'=>1])
			    	->andWhere(['mer_registrarApprove_status'=>NULL])
			    	->all();
			}else if (Yii::$app->user->can('DepartmentHead')) {
				$instDept = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id])->inst_dep_relId;

	    		$requstedCourses = \backend\models\MakeupExamRequest::find()
				    	->where([
				    		'mer_acadYear_relId'=>$this->acadYearId,
				    		'mer_sem_relId'=>$this->semId,
				    	])
				    	->innerJoin('course','course.id = makeup_exam_request.mer_course_relId')
				    	->andWhere(['course.course_dept_relId'=>$instDept])
				    	->andWhere(['mer_approved'=>NULL])
				    	->andWhere(['mer_registrarApprove_status'=>NULL])
				    	->all();
			}else{
				echo "<p class='bg-warning'>Un-privelliged User.</p>";
			}


		  	if ($requstedCourses) {

		  	echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
		    echo "<caption><p class='bg-success'> <i class='bg-danger'>". count($requstedCourses) ."</i> Course(s).</p></caption>";
		    echo "<thead>
		    <th>Reg. ID</th>
		    <th>Full Name</th>
		    <th>Course Title</th>
		    <th>Reason</th>
		    <th>Option</th>
		    </thead><tbody>";
			foreach ($requstedCourses as $course) {
				$std = \backend\models\Student::findOne(['id'=>$course['mer_std_relId']]);
				$user = \backend\models\User::findOne(['id'=>$std->std_user_relId]);
				$reason = \backend\models\MakeupExamReason::findOne(['id'=>$course->mer_reason_relId])->mer_desc;
			    $c = \backend\models\Course::findOne(['id'=>$course->mer_course_relId]);
		
			    $status = is_null($course->mer_approved)?'Pending':'approved';
			    echo "<tr>
			    <td>{$std->std_regId}</td>
			    <td>{$user->first_name} {$user->middle_name}</td>
			    <td>". $c->course_title ."</td>
			    <td>{$reason}</td>
			    <td>
			    	<select onchange='changeStatus(". $course['id'] .",$(this).val())'>
			    		<option value=''>-</option>
			    		<option value='1'>Approve</option>
			    		<option value='0' title='The course grade for this student will be changed to F grade if you choose this option.'>Dis-approve</option>
			    	</select>
			    </td>
			    </tr>";
			}
			echo "</tbody></table>";

		  }else{
		  	echo "<p class='bg-warning'>No Request.</p>";
		  }
    	}
    	die();
    }
}
