<?php

namespace frontend\controllers;
use yii;
use frontend\models\PreRequisitController;
use backend\models\Course;
use frontend\models\SemesterRegistration2;
use frontend\models\SemesterRegistration2Detail;

class PenalityRegistartionController extends \yii\web\Controller
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

    // enroll selected courses for the student
    public function actionEnrollStudentCourses(){
    	$message = '';
    	$tab = '';
    	$studentId = isset($_POST['studentId']) && !empty($_POST['studentId'])?$_POST['studentId']:die('Uknown Student detail.');
    	$eCourseListString = isset($_POST['eCourseListString']) && !empty($_POST['eCourseListString'])?$_POST['eCourseListString']:die('Course List empity.');
    	
    	$eCourseList = json_decode($eCourseListString );
    	$student  = \backend\models\Student::findOne(['id'=>intval($studentId)]);
    	$gset = $this->currentStudentGroup($student);

    	if ($eCourseList && count($eCourseList)) {
    		$tab = "<table class='table table-condensed table-striped table-xs table-bordered'>
    				<thead>
    					<th>Course</th>
    					<th>Status</th>
    				</thead>
    				<caption style='bg-success'>Course Enrollment status</caption>
    				<tbody>";
    		foreach ($eCourseList as $key) {
    			// $key is courseOfferingDetail Id.
    			$coffDet = \backend\models\CourseOffering2Detail::findOne(['id'=>$key]);
    			$pass = FALSE;
		        $message = '';

		        $course2 = Course::findOne(['id'=>$coffDet->coff2det_course_relId]);
		        $precModel = new \backend\models\PreRequisitController();
                $prec = $precModel->safeOfPrerequisit($course2,$student,$gset);
                $message .= $prec[1];
                $pass = $prec[0];
                

		      if ($pass) {
		      	$semregId = $this->getRegEntry($student,$gset);

		      	if (is_numeric($semregId)) {
		      		$model = new SemesterRegistration2Detail();
	                $model->srd2_semreg_relId = $semregId;
	                $model->srd2_coff_relId = $coffDet->coff2det_coff2_relId;
	                $model->srd2_course_relId = $course2->id;
	                $model->srd2_created_at = $model->srd2_updated_at = date('Y-m-d h:i:s');
	                $model->srd2_created_by=$model->srd2_updated_by = Yii::$app->user->identity->id;
	                $model->srd2_group_relId = $gset->id;

	                if ($model->save()) {
	                    $message = 'Enrolled Successfuly';
	                    $status = 1;                   
	                }else{
	                    //print_r($model->errors);die();
	                    $message = 'Enrolling failed';
	                    $status = 0; 
	                }
		      	}else{
		      		$status = 0;
		      		$message = $semregId;
		      	}

                $bg = $status?'bg-success':'bg-danger';
                $tab .= "<tr class=".$bg."><td>". $course2->course_title ."</td><td>". $message ."</td></tr>";
		      }else{
		      	$tab .= "<tr class='warning'>
			      	<td>{$course2->course_title}</td>
			      	<td>{$message}</td>
		      			</tr>";
		      	//echo $message;
		      }
    		}
    		$tab .= "</tbody></table>";
    	}else{
    		echo "<p class='bg-danger'>Course List empty. please re-select the courses.</p>";
    	}

    	// echo "ENROLLING FOR: ".$studentId;
    	// die();
    	echo $tab;
    }

    // Get registration Entry for the student
    protected function getRegEntry($studetObject,$gsetObject){
    	$this->__constructor();
    	 $modelId = NULL;
    	$regRec = SemesterRegistration2::findOne([
                        'semreg_std_relId'=>$studetObject->id,
                        'semreg_semester_relId'=>$this->semId,
                        'semreg_acadYear_relId'=>$this->acadYearId,
                    ]);
      	if (empty($regRec)) {
            $model = new SemesterRegistration2();
            $model->semreg_std_relId = $studetObject->id;
            $model->semreg_semester_relId = $this->semId;
            $model->semreg_acadYear_relId = $this->acadYearId;
            $model->semreg_clslvl_relId = $gsetObject->gs_clslvl_relId;
            $model->semreg_created_by = $model->semreg_updated_by = Yii::$app->user->identity->id;
            $model->semreg_created_at = $model->semreg_updated_at = date('Y-m-d h:i:s');
            if ($model->save()) {
                $modelId = $model->id;
            }else{
                $modelId .= "<tr class='bg-danger'><td colspan=2>".$model->error."</td></tr>";
            }
        }else{
            $modelId = $regRec->id;
        }
        return  $modelId;
    }
    // Get Student courses offered for the student.
    public function actionGetStudentCourse(){
    	$studentId = isset($_POST['studentId']) && !empty($_POST['studentId'])?$_POST['studentId']:die('Uknown Student detail.');
    	$gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId'])?$_POST['gsetId']:die('Uknown Group detail.');

    	$tab = '';

    	// get course that are offered for this group
    	$student  = \backend\models\Student::findOne(['id'=>intval($studentId)]);
    	$gset = \backend\models\WGroupSetup::findOne(['id'=>intval($gsetId)]);

    	$this->__constructor();
    	// ->with('srd2SemregRel')
         // this semester enrolled courses
          $thisSemesterEnrolledCourses = SemesterRegistration2Detail::find()
            ->innerJoinWith('srd2SemregRel','semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
            ->where(['semester_registration_2.semreg_std_relId'=>$student->id,'semester_registration_2.semev_acadYear_relId'=>$this->acadYearId,'semester_registration_2.semev_sem_relId'=>$this->semId])
            ->all();

            $thisSemesterEnrolledCoursesArrays = [];
            if ($thisSemesterEnrolledCourses && count($thisSemesterEnrolledCourses) > 0) {
              foreach ($thisSemesterEnrolledCourses as $key) {
                $thisSemesterEnrolledCoursesArrays[] = $key['srd2_course_relId'];
              }
            }
    	// $enrolledCourses = \frontend\models\SemesterRegistration2Detail::find()
    	// 	->innerJoin('semester_registration_2','semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
    	// 	->where([
    	// 		'semester_registration_2.semreg_acadYear_relId'=>$this->acadYearId,
    	// 		'semester_registration_2.semreg_semester_relId'=>$this->semId,
    	// 		'semester_registration_2.semreg_std_relId'=>$student->id])
    		
    	// 	->andWhere(['semester_registration_2_detail.srd2_grade_relId'=>NULL])
    	// 	->all();
    	// $enrolledCoursesArray = [];
    	// if ($enrolledCourses && count($enrolledCourses) > 0) {
    	// 	foreach ($enrolledCourses as $key) {
    	// 		$enrolledCoursesArray[] = $key['srd2_course_relId'];
    	// 	}
    	// }

    	$offeredCourses = \backend\models\CourseOffering2Detail::find()
    		->innerJoin('course_offering2','course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
    		->where([
    			'course_offering2.coff2_acadYear_relId'=>$this->acadYearId,
    			'course_offering2.coff2_sem_relId'=>$this->semId,
    			'course_offering2.coff2_group_relId'=>$gset->id])
    		->andWhere(['NOT IN','course_offering2_detail.coff2det_course_relId',$thisSemesterEnrolledCoursesArrays])
    		->andWhere(['IS NOT','course_offering2_detail.coff2det_inst_relId',NULL])
    		->with('coff2detCoff2Rel')
    		->all();
    	// and those not enrolled by the student.

    	// if list contains any, display it with checkbox.
    	if ($offeredCourses && count($offeredCourses) > 0) {
    		$tab.= "<table style='font-size:10pt;' class='table table-hover table-xs table-bordered'>";
            $tab.= "<thead style='background-color:#99E;'>
            	<th>#</th>
            	<th>Course Detail</th>
            	<th>CrdtHr.</th>
            	<th>From</th>
            	<th>Option</th>
            	</thead>
            	<tbody>";
            	$c = 0;
            foreach ($offeredCourses as $coff2) {
                $course = \backend\models\Course::findOne(['id'=>$coff2['coff2det_course_relId']]);
                $fdept = \backend\models\Department::findOne(['id'=>$course->course_dept_relId])->dept_name;

                if (in_array($course->id, $thisSemesterEnrolledCoursesArrays)) {
                    continue;
                }
                $tab.= "<tr>
                    <td>".++$c."</td>
                    <td>".$course->course_code." => ".$course->course_title."</td>
                    <td>".$course->course_crdthr."</td>
                    <td>".$fdept."</td>
                    <td>
                    <input type='checkbox' onchange='addEnroll(". $course->id .",". $coff2['id'] .")'>
                    </td>
                    </tr>";
            }
            $tab .= "<input id='wonde_to_ID' type='hidden' value='{$student->id}'";
            $tab .= "</tbody></table>";

            echo $tab;
    	}else{
    		echo "<p class='bg-danger'>No course offered.</p>";die();
    	}
    }


    // Get payed students for penality registration.
    public function actionGetPayedStudents(){//for registrar staff

    	// get those who payed for penality registration in this semester

    	// penality paymnet id is:
    	$paymentType = \backend\models\PaymentReason::findOne(['pr_desc'=>'Penalty registration']);

    	if (!($paymentType && count($paymentType) > 0)) {
    		die('Uknown Payment Type!');
    	}else{
    		$this->__constructor();

			// 
    		$payentStatus = \backend\models\SelfSponsoredPaymentHistory::find()->where([
    			'ssph_acadYear_relId'=>$this->acadYearId,
    			'ssph_sem_relId'=>$this->semId,
    			'ssph_pymntRsn_relId'=>$paymentType->id,
    		])->all();

    		$tab = '';

    		if ($payentStatus && count($payentStatus) > 0) {
    			// Make Table here.
    			$tab .= "<table class='table table-striped table-hover table-condensed table-sm table-bordered'>";
			    $tab .= "<caption><p class='bg-success'> <i class='bg-danger'>". count($payentStatus) ."</i> Requests found for penality registration.</p></caption>";
			    $tab .= "<thead style='text-align:center;'>
			    <th>#</th>
			    <th>RegId</th>
			    <th>Full Name</th>
			    <th>Group</th>
			    <th>Status</th>
			    <th>Action</th>
			    </thead><tbody>";
			    $count = 0;
				foreach ($payentStatus as $key) {
				    $student = \backend\models\Student::findOne(['id'=>$key->ssph_std_relId]);
				    $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
				    $gset  =$this->currentStudentGroup($student);
				    if (!($gset && count($gset) > 0)) {
				    	continue;
				    }
				    $status = '';
				    $bg = '';
				    $action = '?';
				    if (is_null($key->ssph_approved)) {
				    	// not approved yet
				    	$status = 'Pending';
				    	$bg = 'warning';
				    }else{
				    	if ($key->ssph_approved == 1) {
				    		// Approved
                            if ($key->ssph_registrarApproved == 1) {
                                $status = 'Approved';
                                $bg = 'success';
                                $action = "<button class='btn btn-success btn-sm' onclick='enrollCourseFor($student->id,$gset->id)'>Enroll Courses</button>";
                            }else{
                                $status = 'Rejected by Registrar';
                                $bg = 'danger';
                            }
				    	}else{
				    		// Rejected request.
				    		$status = 'Rejected by CEP Clerk';
				    		$bg = 'danger';
				    	}
				    }
				    $tab .= "<tr class = '$bg'>
				    <td style='text-align:center;'>". ++$count."</td>
				    <td>". $student->std_regId ."</td>
				    <td>". $usr->first_name.' '. $usr->middle_name ."</td>
				    <td style='text-align:center;'>". \backend\models\WGroup::findOne(['id'=>$gset->gs_group_relId])->group_name ."</td>
				    <td>". $status."</td>
				    <td>". $action."</td>
				    </tr>";
				}
				$tab .= "</tbody></table>";
				echo $tab;die();
    		}else{
    			echo "<p class='bg-warning'>No Such payment made by any student yet.</p>";
    		}
    	}

    	// if pyament approved , enable enroll courses for those payed and approved students

    	// else show that the request is not approved yet.
    	// echo 1;
    }

    // get student group for this semester.
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


}
