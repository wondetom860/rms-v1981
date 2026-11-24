<?php

namespace frontend\controllers;
use common\components\CommonControllerFunctions as CCF;
use yii;
use yii\filters\VerbFilter;

class StudentSemesterGradeReportController extends \yii\web\Controller
{

	/**
     * {@inheritdoc}
     */
    public function behaviors()
    {
        return [
            'verbs' => [
                'class' => VerbFilter::class,
                'actions' => [
                    'delete' => ['POST'],
                ],
            ],

            'access' => [
               'class' => \yii\filters\AccessControl::class,
               // We will override the default rule config with the new AccessRule class
               'ruleConfig' => [
                   'class' => \yii\filters\AccessRule::class,
               ],
               'rules' => [
                   [
                       'actions' => [],
                       'allow' => true,
                       'roles' => ['@'],
                   ],
               ]
           ]
        ];
    }

	public $acadYearId;
    public $semId;
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    public function actionIndex()
    {
	if(Yii::$app->user->can('Admin') || Yii::$app->user->can('Registrar') || Yii::$app->user->can('clerk')){}else{return "<p class='bg-warning'>Invalid Access</p>";}
    	// Yii::$app->session->setFlash('ALTE_MESSAGE',6);
    	$model = new \backend\models\StudentSemesterStatusHistory();
            return $this->render('index', [
                'model' => $model,
            ]);
        // /return $this->render('index');
    }

    public function actionPrintGrade(){

    	$params = isset($_GET['params']) && !empty($_GET['params'])?(string)$_GET['params']:die('No Record found');

    	$params = json_decode($params);
    	$gsetId = isset($params->gsetId) && !empty($params->gsetId)?intval($params->gsetId):die('Uknown Group.');
    	$stdId = isset($params->studentId) && !empty($params->studentId)?intval($params->studentId):die('Invalid Student info...');

    	$gset = \backend\models\WGroupSetup::findOne(['id'=>intval($gsetId)]);

    	$std = \backend\models\Student::findOne(['id'=>intval($stdId)]);

    	$gas = \backend\models\WGroupAssignment::findOne(['gas_gset_relId'=>$gset->id,'gas_std_relId'=>$std->id]);

    	
    	return $this->renderPartial('grade-report-wt_8472',[ 
    		'content'=>($gas)? $this->student_semester_grade($std,$gset,1):'No Record',
    	]);
    }

    // Return the courses with its grade obtained by the student.
    public function actionReportTable(){

    	$gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId'])?intval($_POST['gsetId']):die('Uknown Group.');
    	$stdId = isset($_POST['studentId']) && !empty($_POST['studentId'])?intval($_POST['studentId']):die('Invalid Student info...');

    	$gset = \backend\models\WGroupSetup::findOne(['id'=>intval($gsetId)]);

    	$std = \backend\models\Student::findOne(['id'=>intval($stdId)]);

    	$gas = \backend\models\WGroupAssignment::findOne(['gas_gset_relId'=>$gset->id,'gas_std_relId'=>$std->id]);

    	if($gas){

    	}else{
    		die('Selected Student is no assigned to the that group. please check if it error!. ERROR:WT_2009');
    	}
		if(Yii::$app->user->can('Admin') || Yii::$app->user->can('Registrar') || Yii::$app->user->can('clerk')){
			// safe access
		}else{
			return "<p class='bg-warning'>Invalid Access!</p>";
		}

    	$responseTable = $this->student_semester_grade($std,$gset);

    	if (is_null($responseTable)) {
    		echo "Error semeseter registration for this student.";
    		die();
    	}

    	return $responseTable;

    }

    // Lists course enrolled student in the group.
	public function actionReport(){
		 // initialize current acdaYera and Semester
		    $this->__constructor();

		    $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId'])?intval($_POST['acadYearId']):die('acadYearId Error..');
		    $semId = isset($_POST['semId']) && !empty($_POST['semId'])?intval($_POST['semId']):die('semId Error..');
		    $admId = isset($_POST['admId']) && !empty($_POST['admId'])?intval($_POST['admId']):die('Uknown Admission');
		    $progId = isset($_POST['progId']) && !empty($_POST['progId'])?intval($_POST['progId']):die('Uknown Program');
		    $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])?intval($_POST['clslvlId']):die('Uknown Class Year.');
		    $gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId'])?intval($_POST['gsetId']):die('Uknown Group.');
		    //$requiredStatus = isset($_POST['requiredStatus']) && !empty($_POST['requiredStatus'])?intval($_POST['requiredStatus']):die('Uknown Group.');

		    $gset = '';

		        $gset = \backend\models\WGroupSetup::findOne([
		                'id'=>$gsetId,
		                'gs_acadYear_relId'=>$acadYearId,
		                'gs_sem_relId'=>$semId,
		                'gs_adm_relId'=>$admId,
		                'gs_prog_relId'=>$progId,
		                'gs_clslvl_relId'=>$clslvlId,
		            ]);

		         if (!($gset)) {
		            die('Invalid Group Setup for your selection.');
		        }
		         // Loadd all students who registered for the selected semester in that acdaYear.
		    $students = \frontend\models\SemesterRegistration2::find()
		        ->where([
		            'semreg_acadYear_relId'=>$acadYearId,
		            'semreg_semester_relId'=>$semId,
		        ])
		        ->innerJoin('semester_registration_2_detail', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
				->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')
				->innerJoin('user','user.id = student.std_user_relId');

		        $students = $students->andWhere(['semester_registration_2_detail.srd2_group_relId'=>$gset->id]);
		        $students = $students->with('semesterRegistration2Detail')->orderBy('user.first_name')->all();

		        //  'gas_approved'
		    $students2 = \backend\models\WGroupAssignment::find()
		    		->innerJoin('student','student.id = w_group_assignment.gas_std_relId')
		    		->innerJoin('user','user.id = student.std_user_relId')
		    		->where(['gas_gset_relId'=>$gset->id])
		    		->orderBy('user.first_name')
		    		->all();
		    // var_dump($students);die();
		    $statusTable = '';

		    if ($students2 && count($students2) > 0) {
		        $statusTable .= "<table id='reportTable' class='table table-bordered' style='font-size:9pt;'>";

		        $admission1 = \backend\models\Admission::findOne(['id'=>$admId]);
		        $program1 = \backend\models\Program::findOne(['id'=>$progId]);
		        $clslvl1 = \backend\models\ClassLevel::findOne(['id'=>$clslvlId]);
		        $acadYear1 = \backend\models\SchoolYear::findOne(['id'=>$acadYearId]);
		        $semester1 = \backend\models\Semester::findOne(['id'=>$semId]);

		        $statusTable .= "<caption style='text-align:center;'>             
		        </caption>";
		        $statusTable .= "<thead style='background-color:#ace;'>
		            <th>#</th>
		            <th>RegId</th>
		            <th>Full Name</th>
		            <th>Option</th>
		            </thead><tbody>";
		            $count =0;
		        foreach ($students2 as $student) {
		            $student = \backend\models\Student::findOne(['id'=>$student['gas_std_relId']]);
		            $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);

		            $dept = \backend\models\Department::findOne(['id'=>$gset->gs_dept_relId]);
		            $spec = isset($gset->gs_spec_relId)?\backend\models\Specialization::findOne(['id'=>$gset->gs_spec_relId])->spec_name:'-';

		            $statusTable .=  "<tr>
		            <td>". ++$count ."</td>
		            <td>". $student->std_regId ."</td>
		            <td>". $usr->first_name.' '. $usr->middle_name ."</td>
		            <td style='text-align:center;' class='non-print'> 
		                <input class='btn btn-success btn-xs' value='Show' type='button' onClick=\"
		                    showGrade({$gset->id},{$student->id});
		                \" />
		            </td>
		            </tr>";
		            // $count++;
		        }
	        // $statusTable .= "<tr style='background-color:#dde' class='non-print'>
	        //         <td colspan=4>{$count} record found.</td>
	        //         <td colspan=5><button class='btn btn-sm btn-success' style='float:right;' onclick='saveOperation(); return false;' id='gsdgafdghafd' title='Approve for selected students.'>Save</button>
	        //         </td></tr>";
	        
	        $statusTable .= "</tbody></table>";

	        return $statusTable;
		    }else{
		        die('No student enrolled course in this semester.');
		    }
	}

	protected function student_semester_grade($studentObject,$gsetObject,$print = NULL){
		// $this->__constructor();
    	$student = $studentObject;
    	$studentObject->calculateGPAs();

    	$semster = $gsetObject->gs_sem_relId;
    	$clslvl = $gsetObject->gs_clslvl_relId;

        $gset = CCF::getStudentGroupHistoryPublic($gsetObject->gs_clslvl_relId,$gsetObject->gs_sem_relId,$student->id);

        
        // $gset = $student->stdGroupReal;

        $acadYear1 = \backend\models\SchoolYear::findOne(['id'=>$gset->gs_acadYear_relId]);

    	$gradeHistory = CCF::getSemesterGrade($gset->gs_clslvl_relId,$gset->gs_sem_relId,$student->id,$gsetObject->gs_acadYear_relId);

    	if (!is_array($gradeHistory)) {
    		return $gradeHistory;
    	}
    	
    	// print_r($gradeHistory);die();

    	$clslvlObject = \backend\models\ClassLevel::findOne(['id'=>intval($clslvl)]);
        $clslvlNum = $clslvlObject->clslvl_num;
        $semesterN = \backend\models\Semester::findOne(['id'=>intval($semster)]);
        $semesterNum = $semesterN->semester_name == 'I'?1:($semesterN->semester_name == 'II'?2:$semesterN->semester_name);
		$admission1 = \backend\models\Admission::findOne(['id'=>$gset->gs_adm_relId]);
		$program1 = \backend\models\Program::findOne(['id'=>$gset->gs_prog_relId]);
		$dept = \backend\models\Department::findOne(['id'=>$gset->gs_dept_relId]);
		$spec = is_null($gset->gs_spec_relId)?'-':\backend\models\Specialization::findOne(['id'=>$gset->gs_spec_relId])->spec_name;
		$grp = \backend\models\WGroup::findOne(['id'=>$gset->gs_group_relId]);
        $gradeTable = "";

        $stdUser = \backend\models\User::findOne(['id'=>$student->std_user_relId]);


        $clslvlNum = $clslvlNum == 1?'1<sup>st</sup>':($clslvlNum == 2?'2<sup>nd</sup>':($clslvlNum == 3?'3<sup>rd</sup>':$clslvlNum.'<sup>th</sup>'));
        $gradeTable .= "<div class='container-fluid' style='text-align:center; margin-top:10px;' >";
        $gradeTable .= '<img src="/uploads/'.CLIENT_LOGO.'" class="user-image" height="80" wedth="60" alt="Logo"/>
        			<p class="bg-default"><h5><b>'. strtoupper(Yii::$app->params['client']) .' , '. strtoupper(Yii::$app->params['college']) .'<br>OFFICE OF THE REGISTRAR</b></h5></p>
        				<b><u style="text-align:center;">Examination Grade Report</u></b><br>
        		</div>';

        $gradeTable .= "<table class='table table-hover table-sm table-condensed' style='border:1px solid black; width:99%'>";
        $gradeTable .= "
        				<caption style='text-align:left; font-weight:normal; color:#000;'>
        					<table>
        						<tr>
        						<td><div class='container-fluid' style=' padding-left:20px;'>
        						<p class='bg-default'> 
                            	<b>Full Name:</b><u><i>{$stdUser->getFullName()}</i></u><br>
                            	<b>Reg.No.:</b> <u><i>{$student->std_regId}</u></i><br>
								<b>Program:</b><u><i>{$program1->program_name}</u></i> 
					            <br>
					            <b>Admission:</b><u><i>{$admission1->admission_name}</u></i><br>

					            <b>Class Level:</b><u><i>{$clslvlNum} Year</u></i><br>
                            </p>
        					</div>
        					</td>
        					<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
        					<td><div class='container-fluid'>
        					<p>
								<b>Department :</b><u><i>{$dept->dept_name}</u></i><br>
								<b>Specialization :</b><u><i>{$spec}</u></i><br>
								<b>Group:</b><u><i>{$grp->group_name}</u></i><br>
								<b>Semester:</b><u><i>{$semesterN->semester_name}</u></i><br>
								<b>Academic Year:</b> <u><i>{$acadYear1->sch_year_desc}</u></i><br>
        					</p>
        					</div>
                            </td>
        					</tr>
        					</table>
        					
        					
                        </caption>";
        $gradeTable .= "<thead style='background-color:#ace;'>
                <th style='border: 1px solid black;'>#</th>
                <th style='text-align:center; border: 1px solid black;'>Course Code</th>
                <th style='text-align:center; border: 1px solid black;'> Course Title</th>
                <th style='text-align:center; border: 1px solid black;'>CrdtHr</th>
                <th style='text-align:center; border: 1px solid black;'>Grade</th>
                <th style='text-align:center; border: 1px solid black;'>Grd. Pt.</th>
                <th style='text-align:center; border: 1px solid black;'>Crs. Added</th>
                <th class='non-print' style='text-align:center; border: 1px solid black;'>Apprvd</th>
                ";
            if (is_null($print)) {
            	$gradeTable .= "<th class='non-print' style='text-align:center; border: 1px solid black;'>Eval.</th>";
            }
            $gradeTable .= "
            </thead><tbody>";
        $count = 0;
        $approved = FALSE;
        $satatus1 = 'Uknown';
        if (is_array($gradeHistory)) {
        	foreach ($gradeHistory[0] as $std) {

        		if ($std->courseIsRepeatedSomeWhere()) {
                    continue;
                }

        		$evaltdAll = 1;
        		$evalueeId2 = NULL;
        		$evalueeId1 = NULL;
        		
	            $gid = $std['srd2_grade_relId'];
	            $grade = is_null($gid)?'-':\backend\models\GradeScale::findOne(['id'=>$gid]);
	            $caddda = is_null($std['srd2_coadd_flag'])?'NO':'YES';
	            $rptd = $std['srd2_corptd_flag'] == 0?'':'*';

	            $course = \backend\models\Course::findOne(['id'=>$std['srd2_course_relId']]);

	            $ltrgrd = '';
	            $grpt = 0;
	            if (is_object($grade)) {
	                $ltrgrd = $grade->gs_ltr_grade;
	                $grpt = $grade->gs_gradePoint * $course->course_crdthr;
	            }else{
	                $ltrgrd = $grade;
	                $grpt = 0;
	            }

	            $grpt = is_numeric($grpt) ? sprintf("%0.2f",$grpt):$grpt;
	            // $gset = $std->srd2GroupRel;
	            $gset = CCF::getStudentGroupHistoryPublic($gsetObject->gs_clslvl_relId,$gsetObject->gs_sem_relId,$student->id);
	            // if the course added, take group of addedWith not the student group setup.
	            if (!is_null($std['srd2_coadd_flag'])) { //indicates the course is added.
	               $addHistory = \backend\models\AddHistory::findOne(['id'=>$std['srd2_coadd_flag']]);
	               $addWithGroup = $addHistory->ah_addWithGroup_relId;
	               // $evtd = CCF::checkIfEvaluated($addWithGroup,$course->id,$evalueeId,Yii::$app->user->identity->id);
	               $coff3 = CCF::getCourseInstructorForGroup($addWithGroup, $course->id);
	               $grrrp = $addWithGroup;
	            }else{
	                 $coff3 = CCF::getCourseInstructorForGroup($gset->id, $course->id);
	                 $grrrp = $gset->id;
	            }
	            if ($coff3) {
	                if (is_null($coff3['coff2det_inst_relId'])) {
	                   $evalueeId = NULL;
	                }else{
	                    // $evalueeId = \backend\models\Instructor::findOne(['id'=>$coff3['coff2det_inst_relId']])->inst_user_relId;
	                    $evalueeId = $coff3->coff2det_inst_relId && $coff3->coff2detInstRel ? $coff3->coff2detInstRel->inst_user_relId : null;

	                    $evalueeId2 = is_null($coff3['coff2det_TA_relId']) || (strtoupper($student->stdProgRel->program_name) == 'MASTERS')?NULL:\backend\models\Instructor::findOne(['id'=>$coff3['coff2det_TA_relId']])->inst_user_relId;
	                }
	                
	                
	               $evtd = CCF::checkIfEvaluated($grrrp,$course->id,$evalueeId,$stdUser->id);
	               $evtd2 = is_null($evalueeId2)?1:CCF::checkIfEvaluated($grrrp,$course->id,$evalueeId2,$stdUser->id);
	                if (is_null($evtd) || is_null($evtd2)) {
	                    $evaltdAll = 0;
	                }
	            }

	            $evaltdAll = $std->evaluatedCourseInstructors();

	            $evaltdAll = (string) $evaltdAll;

	            $gradeTable .= "<tr style='border-bottom: 1px solid black;'>
                  <td style='border: 1px solid black;'>". ++$count."</td>
                  <td style='text-align:center; border-right: 1px solid black; border-bottom: 1px solid black;'>". $course->course_code . "</td>
              	  <td style='border-right: 1px solid black; border-bottom: 1px solid black;'>".$course->course_title ."{$rptd}</td>
                  <td style='text-align:center; border-right: 1px solid black; border-bottom: 1px solid black;'>". $course->course_crdthr ."</td>
                  <td style='text-align:center; border-right: 1px solid black; border-bottom: 1px solid black;'>". $ltrgrd ."</td>
                  <td style='text-align:center; border-right: 1px solid black; border-bottom: 1px solid black;'>". $grpt ."</td>
                  <td style='text-align:center; border-right: 1px solid black; border-bottom: 1px solid black;'>".$caddda."</td>
                  <td style='text-align:center; border-right: 1px solid black; border-bottom: 1px solid black;'  class='non-print'>".$std['srd2_gradeApproved']."</td>
                  ";
                if (is_null($print)) {
	            	$gradeTable .= "<td style='text-align:center; border-right: 1px solid black; border-bottom: 1px solid black;'  class='non-print'>{$evaltdAll}</td>";
	            }
                $gradeTable .= "</tr>";
	        }
        }else{
        	$gradeTable .= "{$gradeHistory}";
        }

        $srd2Model = new \frontend\models\SemesterRegistration2Detail();
        // <input type='hidden' value='{$gsetId}' id='_gsetId' />
        $count1 = $count==0?'NO course ':($count == 1?$count.' course ':$count.' courses ');
        $gradeTable .= "</tbody><tfoot style='background-color:#ddf;'>
                <tr>
                <td colspan =9 style='text-align:center;'>
                ";
        if (is_array($gradeHistory) && is_array($gradeHistory[1])) {
            
            $semesterStatusObject = NULL;

            // isset($gradeHistory[1][1][4]) && count($gradeHistory[1][1][4]) > 0
            if (isset($gset)) {
            	$a = $gset->gsProgRel->program_name == 'Masters';
            	// specialCase

            	$satatus1 = $srd2Model->getSemregRecord($student->id,$gset->gs_clslvl_relId,$gset->gs_sem_relId,$gset,NULL,$a)[2];
            	$satatus2 = $srd2Model->getSemregRecordPrev($student->id,$gset,NULL)[2];
            }else{
	            $satatus1 = 'GROUP ERROR';
            }

			$s11 = is_numeric($gradeHistory[1][1][2])?$gradeHistory[1][1][2] * $gradeHistory[1][1][1]:'-';
			// $rrrr = is_numeric($gradeHistory[1][1][2])?round($s11,2,PHP_ROUND_HALF_UP):'-';
			$rrrr = isset($gradeHistory[1][1][5]) && is_numeric($gradeHistory[1][1][5])?round($gradeHistory[1][1][5],2,PHP_ROUND_HALF_UP):'-';
            // $rrrr = is_numeric($gradeHistory[1][1][0])?sprintf("%0.2f",):$gradeHistory[1][1][0];
            $eeee = is_numeric($gradeHistory[1][2][0])?sprintf("%0.2f",round($gradeHistory[1][2][0],2,PHP_ROUND_HALF_UP)):(sprintf("%0.2f",round(($gradeHistory[1][2][1]/$gradeHistory[1][2][2]),2,PHP_ROUND_HALF_UP)));

            $ss33 = $gradeHistory[1][2][1];

            $ss33 = is_numeric($ss33)?round($ss33 * 4) / 4:$ss33;
            $s11 = is_numeric($s11)?round($s11 * 4) / 4:$s11;

            // for the case of rounding to nearest of .25 or .75 place,
            // $ss33 = is_numeric($ss33)?round($ss33 * 2) / 4:$ss33;
            // $s11 = is_numeric($s11)?round($s11 * 2) / 4:$s11;

            $ss22 = is_numeric($gradeHistory[1][0][2])?sprintf("%0.2f",round($gradeHistory[1][0][2],2,PHP_ROUND_HALF_UP)):($gradeHistory[1][0][2] == 0? '-':$gradeHistory[1][0][2]);
            $ss21 = is_numeric($gradeHistory[1][1][1])?sprintf("%0.2f",round($gradeHistory[1][1][1],2,PHP_ROUND_HALF_UP)):$gradeHistory[1][1][1];
            $ss23 = is_numeric($gradeHistory[1][0][1])?sprintf("%0.2f",round($gradeHistory[1][0][1],2,PHP_ROUND_HALF_UP)):$gradeHistory[1][0][1];
            $ss24 = is_numeric($gradeHistory[1][0][0])?sprintf("%0.2f",round($gradeHistory[1][0][0],2,PHP_ROUND_HALF_UP)):$gradeHistory[1][0][0];
            $ss31 = is_numeric($gradeHistory[1][2][2])?sprintf("%0.2f",round($gradeHistory[1][2][2],2,PHP_ROUND_HALF_UP)):$gradeHistory[1][2][2];
            $ss33 = is_numeric($ss33)?sprintf("%0.2f",round($ss33,2,PHP_ROUND_HALF_UP)):$ss33;
            $s11 = is_numeric($s11)?sprintf("%0.2f",round($s11,2,PHP_ROUND_HALF_UP)):$s11;
            if ($std->srd2SemregRel->semreg_gradeCompleted) {
                $semreg = $std->srd2SemregRel;
                $ss31 = sprintf("%0.2f",$semreg->semreg_totalCrdtHr);
                $ss33 = sprintf("%0.2f",$semreg->semreg_gradePoint_sum);
                $eeee = sprintf("%0.2f",round($semreg->semreg_CGPA,2,PHP_ROUND_HALF_UP));
            }
            // ". $gradeHistory[1][1][1] ."  =>Total grade point.
            $gradeTable .= "<table class='table table-sm table-condensed'>
                            <thead style='text-align:right;'>
                                <th> </th>
                                <th>Crdthr</th>
                                <th>Grade point</th>
                                <th>Grade point Avrg.</th>
                            </thead>
                            <tbody style='text-align:center;'>
                            	<tr><td>Previous Total / Commulative</td>
	                            	<td>".$gradeHistory[1][1][2] ."</td>
	                            	<td>". $rrrr ."</td>
	                            	<td>".  $ss21 ."</td>
                            	</tr>
                            	<tr>
	                            	<td>This Semester</td>
	                            	<td>". $ss22 ."</td>
	                            	<td>". $ss23 ."</td>
	                            	<td>". $ss24 ."</td>
                            	</tr>
                            	<tr>
                            		<td>Current Total/Commulative</td>
                            		<td>". $ss31 ."</td>
                            		<td>". $ss33 ."</td>
                            		<td>". $eeee."</td>
                            	</tr>
                            	<tr><td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td></td><td></td></tr>
                            	<tr><td colspan = 2>Prev. Status: <u><b>{$satatus2}</b></u></td><td colspan = 2>Current Status: <u><b>{$satatus1}</b></u></td></tr>
                        	</tbody>
                        </table>";
        }else{
            $gradeTable .= "{$gradeHistory[1]}";
        }

        $gradeTable .= "</td>
        			</tr>
        			<tr>
        				<td></td>
        				<td></td>
        				<td style='text-align:left;'>Registrar</td>
        				<td colspan = 6  style='text-align:center;'>Date Issued:</td>
        			</tr>
        			<tr>
        				<td></td>
        				<td style='text-align:center;'>&nbsp;&nbsp;&nbsp;</td>
        				<td style='text-align:left;'>______________</td>
        				<td colspan = 6  style='text-align:center;'>_____________</td>
        			</tr>
        			<tr>
        				<td></td>
        				<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
        				<td></td>
        				<td colspan = 6></td>
        			</tr>
        			<tr>
        				<td></td>
        				<td>&nbsp;&nbsp;&nbsp;&nbsp;</td>
        				<td></td>
        				<td colspan = 6></td>
        			</tr>
        			<tr>
        				<td colspan = 9 style='font-size:7pt; text-align:right; font-style:italic;'>
        				<br><br><br><br><br>
        				Report generated on ". date('D, M d, Y') ."
        				</td>
        			</tr>
        			</tfoot></table>";

        return  $gradeTable;
        die();
    }
}
