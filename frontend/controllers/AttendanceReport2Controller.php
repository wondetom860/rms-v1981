<?php

namespace frontend\controllers;
use Yii;
use backend\models\CourseOffering2Detail;
use frontend\models\SemesterRegistration2;
use backend\models\Attendance;
use backend\models\Attendancedetail;
use frontend\models\SemesterRegistration2Detail;
use common\components\CommonControllerFunctions AS CCF;

class AttendanceReport2Controller extends \yii\web\Controller
{

	public $acadYearId;
    public $semId;

    protected function __constructor() {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
    public function actionIndex()
    {
        return $this->render('index');
    }


    // get-exam-hall-ticket
    public function actionGetExamHallTicket(){

    	$stdId = isset($_GET['params']) && !empty($_GET['params']) ? intval($_GET['params']) : die("Error, Student Record Entry Error!");

    	$this->__constructor();

    	$semRegs = SemesterRegistration2Detail::find()
	        ->innerJoin('semester_registration_2','semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
	        ->where([
	          'semreg_acadYear_relId'=>$this->acadYearId,
	          'semreg_semester_relId'=>$this->semId,
	          'semreg_std_relId' => intval($stdId),
	        ])
	        ->all();

	    if ($semRegs) {
	    	$adReport = [];
	    	$stdId = intval($stdId);
	    	foreach ($semRegs as $srd2Key) {
		    	$attendanceData = Attendance::find()
		         	->where([
		         		'attendance.a_course_relId' => $srd2Key->srd2_course_relId,
		         		'attendance.a_group_relId' => $srd2Key->srd2_group_relId
		         	])
		         	->all();

		         	// ->andWhere(['ad_student_relId'=>$stdId])
		        if ($attendanceData) {
		        	$p = 0;
		        	$total = 0;
		        	foreach ($attendanceData as $adKey) {
		        		$ahour = $adKey->a_hour;
	                    if (!is_null($adKey->a_presentStudentsList) && in_array($stdId, json_decode($adKey->a_presentStudentsList,true))) {
	                      $status = 'P';
	                      $p +=  $ahour == 0 ? 1:$ahour;
	                    }else{
	                     $status = 'A';
	                    }
	                    $total += $ahour == 0 ? 1:$ahour;
		        	}

		        	$adReport[] = [
		    			'courseId'=>$srd2Key->srd2_course_relId,
		    			'p'=>$p,
		    			'total'=>$total,
		    		];

		    	}else{
		    		$adReport[] = [
		    			'courseId'=>$srd2Key->srd2_course_relId,
		    			'p' => "N/A",
		    			'total' => "N/A",
		    		];
		    	}
		    }
	    }else{
	    	return "Student Semester Registration Entry Error, NOT_ENROLLED_COURSE (?)";
	    }

	    return $this->renderPartial('hall-ticket',['adReport'=>$adReport,'stdId'=>$stdId,]);
    }

    public function actionGetReport(){
    	$gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId'])? intval($_POST['gsetId']):die('Invalid query, Group entry invalid!');
    	$coffId = isset($_POST['coffId']) && !empty($_POST['coffId'])? intval($_POST['coffId']):die('Invalid query, Course Offering Entry Uknown');
    	$courseId = isset($_POST['courseId']) && !empty($_POST['courseId'])? intval($_POST['courseId']):die('Invalid query, Uknown Course');


		$attendanceData = Attendance::find()
          ->where(['attendance.a_course_relId' => $courseId])
          ->andWhere(['attendance.a_group_relId' => $gsetId]);
	      $numberOfClasses = $attendanceData->count();
	      if ($numberOfClasses < 1) {
	          Yii::$app->session->setFlash('message',"No attendance collected for the selected section yet.");
	          return $this->renderPartial('/attendance-report/attendancereport',[
	          	'courseId'=>$courseId,
	          	'groupId'=>$gsetId,
	          ]);
	        //   die(); 
	      }
	      $attendanceData = $attendanceData->all();
	      $this->__constructor();
	      // var_dump($attendanceData);die();
	      // get all the students who enrolled that course .

	      $semRegs = SemesterRegistration2::find()
	        ->innerJoin('semester_registration_2_detail','semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
	        ->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')
	        ->innerJoin('user','user.id = student.std_user_relId')
	        ->where([
	          'semreg_acadYear_relId'=>$this->acadYearId,
	          'semreg_semester_relId'=>$this->semId,
	        ])
	        ->andWhere([
	          'srd2_course_relId'=>intval($courseId),
	          'srd2_group_relId'=>intval($gsetId),
	        ])
	        ->orderBy(['user.first_name' => SORT_ASC,'user.middle_name' => SORT_ASC])
	        ->all();
	        $table = '';
	      if ($semRegs) {
	        $gset = \backend\models\WGroupSetup::findOne($gsetId);

	        $adm = \backend\models\Admission::findOne($gset->gs_adm_relId);
	        $prog = \backend\models\Program::findOne($gset->gs_prog_relId);
	        $dept = \backend\models\Department::findOne($gset->gs_dept_relId);
	        $clslvl = \backend\models\ClassLevel::findOne($gset->gs_clslvl_relId)->clslvl_num;
	        $gname = \backend\models\WGroup::findOne($gset->gs_group_relId)->group_name;
	        $spec = is_null($gset->gs_spec_relId)?'-':\backend\models\Specialization::findOne($gset->gs_spec_relId)->spec_name;
	        $course = \backend\models\Course::findOne($courseId);

	        // $clslvl = $clslvl == 1?$clslvl.'<sup>st</sup>':($clslvl == 2?$clslvl.'<sup>nd</sup>':($clslvl == 3?$clslvl.'<sup>rd</sup>':($clslvl.'<sup>th</sup>')));
	        $clslvl = 'Year-'.$clslvl;
	        $acadYear = Yii::$app->params['C']['Y'];
	        $sem = Yii::$app->params['C']['S'];

	        $inst = 'Course Instructor';

	        // print_r($attendanceData);die();
	        $instModel = \common\models\User1::findOne($attendanceData[0]['a_createdBy']);
	        if ($instModel) {
	        	$inst = $instModel->first_name .' '.$instModel->middle_name[0]."(Course Instructor)";
	        }
	        $table = "<table class='table table-stripped table-hover table-bordered table-condensed' style='font-size:9pt;' > ";
	        $btn = "<button class'btn btn-xs btn-link pull-right' onclick='$(\".hidable\").toggle(\"display\")' title='Show Detail'>[ ? ]</button>";
	        $table .="<caption style='font-weight:bold;'>
	                    <h3 style='text-align:center; '>Student Attendance report</h3>
	                    <p style='text-align:center;'>
	                    Admission:<u><i>{$adm->admission_name}</i></u>
	                    Program:<u><i>{$prog->program_name}</i></u><br>
	                    Department:<u><i>{$dept->dept_name}</i></u>
	                    Specialization:<u><i>{$spec}</i></u><br>
	                    Class Level:<u><i>{$clslvl}</i></u>
	                    Group:<u><i>{$gname}</i></u>
	                    <br>
	                    Semester <u><i>{$sem}</i></u> of <u><i>{$acadYear}</i></u><br>
	                    Course:<u><i>{$course->course_title} -</i></u> Attendance Collected By : <u><i>{$inst}</i></u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;{$btn}
	                    </p>
	                </caption>";
	        $table .= "<thead style='background-color:#8D0;'>
	                  <th>#</th>
	                  <th>Reg. ID</th>
	                  <th>Student Name</th>";
	        foreach ($attendanceData as $aData) {
	          $date = date_format(date_create($aData->a_createdAt),'M d');
	          $table .= "<th class='hidable' style='display:none;'>{$date}<br>(<small>{$aData->a_hour}hr</small>)</th>";
	        }

	        $table .= "<th style='background-color: #EFF; text-align:center;'>Total (P)</th>";
	        $table .= "<th style='background-color: #EFF; text-align:center;'>Total (A)</th>";
	        $table .= "<th style='background-color: #EFF; text-align:center;'>Total(%)</th>";
	        $table .= "<th style='background-color: #FBFEFF; text-align:center;'>Hall Ticket</th>";

	        $table .= "</thead><tbody>";
	                $count = 0;
	        foreach ($semRegs as $std) {
	          $p = 0;
	          $total = 0;
	          $student = \backend\models\Student::findOne($std->semreg_std_relId);
	          $user = \common\models\User1::findOne($student->std_user_relId);
	          $table .= "<tr>
                <td style='text-align:center;'>".++$count."</td>
                <td>{$student->std_regId}</td>
                <td>{$student->getFullName()}</td>";
                foreach ($attendanceData as $aData) {
                	$ahour = $aData->a_hour;
                    if (!is_null($aData->a_presentStudentsList) && in_array($student->id, json_decode($aData->a_presentStudentsList,true))) {
                      $status = 'P';
                      // $hr = $ahour == 0 ? 1:$ahour;
                      $p +=  $ahour;
                    }else{
                     $status = 'A';
                    }
                    $total +=  $ahour == 0 ? 1:$ahour;
                    $table .= "<td style='text-align:center; display:none;' class='hidable'>{$status}</td>";
                  }
	          $g = round($p / $total *100,1);

	          $bbttnn = "<button class='btn btn-xs btn-info' onclick='generateHallTicketReport({$student->id})' >Generate</button>";

	          $table .= "<td style='text-align:center; background-color: #DFF;'>{$p}</td>";
	          $table .= "<td style='text-align:center; background-color: #DFF;'>".($total - $p)."</td>";
	          $table .= "<td style='text-align:center; background-color: #DFF;'>{$g}% (P)</td>";
	          $table .= "<td style='text-align:center; background-color: #FAFDFF;'>{$bbttnn}</td>";
	          $table .= "</tr>";
	        }
	        $table .= "</tbody></table>";
	        return $table;
	      }else{
	        Yii::$app->session->setFlash('message',"No record found.");
	        return $this->redirect('/attendance-report/printattendancereport');
	      }
    }

    public function actionGetGroup(){
    	$gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId'])?intval($_POST['gsetId']):die('Uknown Group setup enrty. Please try again...');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])?intval($_POST['clslvlId']):die('Uknown Class Level. Please try again...');
        $deptId = isset($_POST['deptId']) && !empty($_POST['deptId'])?intval($_POST['deptId']):die('Uknown Department. Please try again...');
        // $groupId = isset($_POST['groupId']) && !empty($_POST['groupId'])?intval($_POST['groupId']):die('Uknown Department. Please try again...');

        // get and retrun the courses offered for the selected group

        $this->__constructor();
        $groups = \backend\models\WGroupSetup::find()
            ->where([
                'gs_acadYear_relId'=>$this->acadYearId,
                'gs_sem_relId'=> $this->semId,
                'gs_dept_relId'=>$deptId,
                'gs_clslvl_relId'=>$clslvlId,
            ])
            ->all();
        if ($groups && count($groups) > 0) {
            echo "<div class='panel panel-default'><div class='panel-heading'><div class='row' ><div class='col-md-9'> ";
            $gsets = [];
            foreach ($groups as $key) {
                $group = \backend\models\WGroup::findOne($key->gs_group_relId);
                $spec = isset($key->gs_spec_relId)?\backend\models\Specialization::findOne($key->gs_spec_relId)->spec_name:'';
                echo "<button class='btn btn-link' onclick='loadStudents({$key->id},{$deptId},{$clslvlId},{$group->id})'>{$group->group_name}[$spec]</button>";
                $gsets[] = $key->id;
                //put a right aligned seelction for filtering Courses offered for that group.
            }
            echo "</div><div class='col-md-3'>
                <select class='form-control'onchange='loadStudentsS($(this).val());' id='deptidddd'>
                    ";
                $coffs = CourseOffering2Detail::find()
                	->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
                	->where(['coff2_group_relId'=>$gsets])
                	->groupBy('coff2det_course_relId')
                	->all();
                if ($coffs && count($coffs) > 0) {
                	foreach ($coffs as $COFFkey) {
                		echo "<option data-coff='{$COFFkey->id}' value='{$COFFkey->coff2det_course_relId}'>{$COFFkey->coff2detCourseRel->course_title}</option>";
                	}
                }
            echo "</select>
            </div></div>";
            echo "</div><div class='panel-body' id='StudentContainer'></div></div>";
        }else{
            echo "<p class='bg-danger'>No record found</p>";die();
        }
    }

    protected function getPresentCount($detailIds, $studentId) {
        //find Number of presents for each day
        $presnts = Attendancedetail::find()
                ->where(['ad_attendance_relId' => $detailIds])
                ->andWhere(['attendancedetail.ad_student_relId' => $studentId])
                ->count();
        return $presnts;
    }


}
