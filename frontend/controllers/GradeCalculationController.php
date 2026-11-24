<?php

namespace frontend\controllers;
use yii;
use backend\models\StudentGroupHistory;
use backend\models\SchoolYear;
use backend\models\Semester;
use backend\models\Student;
use backend\models\User;
use backend\models\GradeScale;
use backend\models\CourseOffering;
use frontend\models\SemesterRegistration2;
use frontend\models\SemesterRegistration2Detail;
use \common\components\CommonControllerFunctions as CCF;

class GradeCalculationController extends \yii\web\Controller
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
    	$model = new StudentGroupHistory();
        return $this->render('index',['model'=>$model,]);
    }

    public function actionCalculategrade(){
    	// remember, you have to handle also for another semester and academic year. 
    	// The default is current semester and academic year.

    	$this->__constructor();
    	if (isset($_POST['stdId'])) {
    		$stdId = $_POST['stdId'];
    		$std = Student::findOne(['id'=>$stdId]);
    		$semRegRecord = $this->getSemregRecord($stdId,$this->acadYearId,$this->semId);
    		if (!empty($semRegRecord) && count($semRegRecord) >0) {
    			$semRegRecordID = $semRegRecord->id;
    			$enrolledCourses = $this->getEnrolledCourses($semRegRecord);
    			if (!empty($enrolledCourses) && count($enrolledCourses) >0) {
    				$enrolledCoursesCount = count($enrolledCourses);
    				echo "<p class='bg-success'>Enrolled ". $enrolledCoursesCount ." courses this semester.<button class='btn btn-link btn-sm' onclick='printCont(". 'print_grpoint' .")' style='float:right;'>print</button></p>";
    				$this->drawTable($enrolledCourses,$std,$semRegRecord);
    			}else{
    				echo "<p class='bg-danger'>The selected Student didn't enrolled any courses for this semester.</p>";
    			}
    		}else{
    			echo "<p class='bg-danger'>The selected Student didn't enrolled any courses for this semester.</p>";
    		}
    		die();
    	}
    }
    protected function generateCGPA(){

    }


    protected function generateSGPA(){

    }

    protected function getGradedCourseCount($enrolledCourses){
    	$gCount = 0;
    	if (!empty($enrolledCourses)) {
    		foreach ($enrolledCourses as $enrolledCourse) {
    			if (isset($enrolledCourse['srd2_grade_relId'])) {
    				$gCount +=1;
    			}
    		}
    	}
    	return $gCount;
    }

    protected function getGradePoint($grade,$crdthr){
    	return $grade * $crdthr;
    }

    protected function getCourseGrade($enrolledCourse){
    	$srd2GradeId = $enrolledCourse['srd2_grade_relId'];
    	$gradeDet = $srd2GradeId;
    	if (!is_null($gradeDet)) {
    		$gradeDet = GradeScale::findOne(['id'=>$srd2GradeId]);
    	}

    	return $gradeDet;
    }

    protected function drawTable($enrolledCourses,$std,$semRegRecord){

    	$usr = User::findOne(['id'=>$std->std_user_relId]);
    	$spec = is_null($std->std_spec_relId)?'-':\backend\models\Specialization::findOne(['id'=>$std->std_spec_relId])->spec_name;
        $grp = is_null($std->std_group_realId)?'Not assigned to any group in this semester.':\backend\models\StudentGroup::findOne(['id'=>$std->std_group_realId])->group_name;
        $fullname = $usr->first_name.' '.$usr->last_name;
        echo "<div id='print_grpoint' style='border:1px dashed black;'>";
        echo "<table class='table table-hover table-sm table-condensed'>";
            echo "<tr><td colspan=2>
                <b>Academic Year: </b>". Yii::$app->params['C']['Y'] ."    <br><b>Current Semester: </b>". Yii::$app->params['C']['S'] ."     <hr>
                <b>Admission:</b> ". \backend\models\Admission::findOne(['id'=>$std->std_admission_relId])->admission_name ."
                <br>
                <b>Program:</b> ". \backend\models\Program::findOne(['id'=>$std->std_prog_relId])->program_name ."
                <br>
                <b>Department: </b>". \backend\models\Department::findOne(['id'=>$std->std_dept_relId])->dept_name ."      <br><b>Class Year: </b>". \backend\models\ClassLevel::findOne(['id'=>$std->std_clslvl_relId])->clslvl_name  ."   <br>
                <b>Specialization: </b>". $spec ."
                <br>
                <b>Group: </b>". $grp ."
            </td>
            <td colspan=2>
                <b>Full Name: </b>". $fullname ."
                <br>
                <b>Reg.ID.: </b>". $std->std_regId ."
                <br>
            <td></tr></table>";
            $count = 0;
            $crdtHrSum = 0;
            $grPointSum = 0;
            $gradedCourseCount = $this->getGradedCourseCount($enrolledCourses);
            $rem = count($enrolledCourses) - $gradedCourseCount;
            if ($rem  > 0) {
            	echo "
            			<p class='bg-danger'>System is waiting for <i style='color:red;'>". $rem ."</i> course(s) grade.</p>
            		";
            		die();
            }else{
            	echo "<table class='table table-hover table-sm table-bordered table-condensed'>";
		        echo "<thead>
		            <th>#</th>
		            <th>Course Code</th>
		            <th>Course Title</th>
		            <th>Crdthr</th>
		            <th>Grade</th>
		            <th>GrPoint</th>
		            </thead><tbody>";
    	        foreach ($enrolledCourses as $enrolledCourse) {
		        	if (isset($enrolledCourse['srd2_grade_relId'])) {
		        		$gradeDet = $this->getCourseGrade($enrolledCourse);
		        	}else{
		        		continue;
		        	}
		        	
		        	$course = \backend\models\Course::findOne(['id'=>$enrolledCourse['srd2_course_relId']]);
		        	$grpt = $this->getGradePoint($course->course_crdthr,$gradeDet->gs_gradePoint);
		        	echo "
		        		<tr>
		        		<td>".++$count."</td>
		        		<td>". $course->course_code ."</td>
		        		<td>". $course->course_title ."</td>
		        		<td>". $course->course_crdthr ."</td>
		        		<td>". $gradeDet->gs_ltr_grade ."</td>
		        		<td>". $grpt ."</td>
		        		</tr>
		        	";
		        	$crdtHrSum += $course->course_crdthr;
		        	$grPointSum += $grpt;

	        }
            }

            // get SemRegRecord of student on previous semester. <::>
            

            $SGPA = $grPointSum/$crdtHrSum;
            $totalCrdtHr = $semRegRecord->semreg_totalCrdtHr + $crdtHrSum;
            $totalGP = $semRegRecord->semreg_totalCrdtHr * $semRegRecord->semreg_CGPA;
            $SEM_TOTAL_GP = $totalGP + $grPointSum;
            $CGPA = ($SEM_TOTAL_GP) / $totalCrdtHr;

        echo "</tbody></table>";
        echo "<table class='table table-sm table-condensed'>
        	<thead>
	            <th> </th>
	            <th>Crdthr</th>
	            <th>Grade point</th>
	            <th>Grade point Avrg.</th>
	            </thead><tbody style='text-align:center;'>
        	<tr>
        		<td>Previous Total / Commulative</td>
        		<td>".$semRegRecord->semreg_totalCrdtHr."</td>
        		<td>". round($totalGP, 2, PHP_ROUND_HALF_UP) ."</td>
        		<td>". $semRegRecord->semreg_CGPA ."</td>
        	</tr>
        	<tr>
        		<td>This Semester</td>
        		<td>". $crdtHrSum ."</td>
        		<td>". $grPointSum ."</td>
        		<td>". round($SGPA, 2, PHP_ROUND_HALF_UP) ."</td>
        	</tr>
        	<tr>
        		<td>Current Total/Commulative</td>
        		<td>". $totalCrdtHr ."</td>
        		<td>". round($SEM_TOTAL_GP,2,PHP_ROUND_HALF_UP)."</td>
        		<td>". round($CGPA,2,PHP_ROUND_HALF_UP)."</td>
        	</tr>
        </tbody></table>";

        echo "</div>";

    }

    protected function getEnrolledCourses($semRegRecord){
    	$enrCourses = null;
    	if (!empty($semRegRecord) && count($semRegRecord) >0) {
    		$enrCourses = SemesterRegistration2Detail::find()->where([
    			'srd2_semreg_relId'=>$semRegRecord->id,
    		])->all();
    	}
    	return $enrCourses;
    }

    protected function getSemregRecord($stdId,$acadYearId,$semId){

    	$semreg2 = SemesterRegistration2::findOne([
                        'semester_registration_2.semreg_acadYear_relId'=>$acadYearId,
                        'semester_registration_2.semreg_semester_relId'=>$semId,
                        'semester_registration_2.semreg_std_relId' =>$stdId,
                    ]);
    	return $semreg2;
    }

}
