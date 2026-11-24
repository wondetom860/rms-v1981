<style>
	.diagonal-text span {
		transform: rotate(-52deg);
		white-space: nowrap;
		position: absolute;
		top: 50%;
		left: 40%;
		font-size: 3em;
		font-style: italic;
		font-weight: bold;
		font-family: 'Courier New', Courier, monospace;
		opacity: 0.4;
	}
</style>
<?php
/**
 * @var yii\web\View $this
 * @var backend\models\Student $studentObject
 * @var backend\models\WGroupSetup $gsetObject
 * @var backend\models\SemesterRegistration2 $semregModel
 */

use common\components\CommonControllerFunctions as CCF;
// use backend\models\SemesterEvaluation2;

if (isset($studentObject) && isset($gsetObject) && !empty($gsetObject) && !empty($studentObject)) {
	echo student_semester_grade($studentObject, $gsetObject, $semregModel, 1);
} else {
	echo "InvalId Request!";
	die();
}

function student_semester_grade($studentObject, $gsetObject, $semregModel, $print = NULL)
{
	// $this->__constructor();
	$student = $studentObject;
	$studentObject->calculateGPAs();

	$semster = $gsetObject->gs_sem_relId;
	$clslvl = $gsetObject->gs_clslvl_relId;

	$gset = CCF::getStudentGroupHistoryPublic($gsetObject->gs_clslvl_relId, $gsetObject->gs_sem_relId, $student->id, $gsetObject->gs_acadYear_relId);

	$gset = $gset ? $gset : $gsetObject;

	// $gset = $student->stdGroupReal;

	$acadYear1 = \backend\models\SchoolYear::findOne(['id' => $gset->gs_acadYear_relId]);

	CCF::getSemesterGrade($gset->gs_clslvl_relId, $gset->gs_sem_relId, $student->id, $gset->gs_acadYear_relId, $gset);
	$gradeHistory = CCF::getSemesterGrade($gset->gs_clslvl_relId, $gset->gs_sem_relId, $student->id, $gset->gs_acadYear_relId, $gset);
	// $gradeHistory = $semregModel->getSemesterStatus($gset);
	if (!is_array($gradeHistory)) {
		return $gradeHistory;
	}

	// print_r($gradeHistory[1][1]);die();
	// print_r($gradeHistory[1][1][3][0]->sssccdClslvlRel->getClslvlDisp());die();

	$clslvlObject = \backend\models\ClassLevel::findOne(['id' => intval($clslvl)]);
	// $clslvlNum = $clslvlObject->clslvl_num;
	$semesterN = \backend\models\Semester::findOne(['id' => intval($semster)]);
	// $semesterNum = $semesterN->semester_name == 'I'?1:($semesterN->semester_name == 'II'?2:$semesterN->semester_name);
	$admission1 = \backend\models\Admission::findOne(['id' => $gset->gs_adm_relId]);
	$program1 = \backend\models\Program::findOne(['id' => $gset->gs_prog_relId]);
	$dept = \backend\models\Department::findOne(['id' => $gset->gs_dept_relId]);
	$spec = is_null($gset->gs_spec_relId) ? '-' : \backend\models\Specialization::findOne(['id' => $gset->gs_spec_relId])->spec_name;
	$grp = \backend\models\WGroup::findOne(['id' => $gset->gs_group_relId]);
	$gradeTable = "";

	$stdUser = \backend\models\User::findOne(['id' => $student->std_user_relId]);


	// $clslvlNum = $clslvlNum == 1?'1<sup>st</sup>':($clslvlNum == 2?'2<sup>nd</sup>':($clslvlNum == 3?'3<sup>rd</sup>':$clslvlNum.'<sup>th</sup>'));
	// $clslvlNum = "[}]";
	// <img src="/uploads/'.CLIENT_LOGO.'" class="user-image" height="80" wedth="60" alt="Logo"/>
	// <p class="bg-default"><h5><b>'. strtoupper(Yii::$app->params['client']) .' , '. strtoupper(Yii::$app->params['college']) .'
	// $cls = $gset->gsActiveProgramRel;

	// Examination Grade Report 6/13/24


	$gradeTable .= "<div class='container-fluid diagonal-text' style='text-align:center;' >";
	$entry = $gradeHistory[0][0]->srd2SemregRel->semreg_entry_type == 2 ? "Transfer" : '';
	$gradeTable .= "<span>{$entry}</span>";
	$gradeTable .= '<h5><b>OFFICE OF THE REGISTRAR</b></h5></p>
        				<b><u style="text-align:center;">Semester Grade Report</u></b><br>
        		</div>';

	$gradeTable .= "<table class='table table-hover table-sm table-condensed' cellspacing=0 cellpdding=4 style='border:1px solid black; width:99%'>";
	$gradeTable .= "
			<caption style='text-align:left; font-weight:normal; color:#000;'>
				<table>
					<tr>
					<td><div class='container-fluid' style=' padding-left:20px;'>
					<p class='bg-default'> 
                	<b>Full Name :</b><u><i>{$student->getFullName()}</u></i><br>
                	<b>Reg.No. :</b> <u><i>{$student->std_regId}</u></i><br>
					<b>Program :</b><u><i>{$admission1->admission_name} {$program1->program_name}</u></i> 
		            <br>
		            <b>Class Level :</b><u><i>{$clslvlObject->getClslvlDisp()}</i></u><br>
                </p>
				</div>
				</td>
				<td>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
				<td><div class='container-fluid'>
				<p>
					<b>Department :</b><u><i>{$dept->dept_name}</u></i><br>
					<b>Specialization :</b><u><i>{$spec}</u></i><br>
					<b>Group:</b><u><i>{$grp->group_name}</u></i><br>
					<b>Academic Year:</b> <u><i>{$acadYear1->sch_year_desc}-{$semesterN->semester_name}</u></i><br>
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
			if ($std->courseIsRepeatedSomeWhere()) { //what if repeated in later registration
				continue;
			}
			// if ($std->srd2_corptd_flag) {
			// 	continue;
			// }
			$evaltdAll = 1;
			$evalueeId2 = NULL;
			$evalueeId1 = NULL;

			$gid = $std['srd2_grade_relId'];
			$grade = is_null($gid) ? '-' : $std->srd2GradeRel;
			$caddda = is_null($std['srd2_coadd_flag']) ? 'NO' : 'YES';
			$rptd = $std['srd2_corptd_flag'] == 0 ? '' : '*';

			$course = \backend\models\Course::findOne(['id' => $std['srd2_course_relId']]);

			$ltrgrd = '';
			$grpt = 0;
			if (is_object($grade)) {
				$ltrgrd = $grade->gs_ltr_grade;
				$grpt = $grade->gs_gradePoint * $course->course_crdthr;
			} else {
				$ltrgrd = $grade;
				$grpt = 0;
			}

			// format grade point:
			$grpt = sprintf("%0.2f", $grpt);

			// $gset = $std->srd2GroupRel;
			// $gset = CCF::getStudentGroupHistoryPublic($gsetObject->gs_clslvl_relId, $gsetObject->gs_sem_relId, $student->id,$gsetObject->gs_acadYear_relId);
			// $gset = $std->srd2GroupRel;
			// if the course added, take group of addedWith not the student group setup.
			if (!is_null($std['srd2_coadd_flag'])) { //indicates the course is added.
				$addHistory = \backend\models\AddHistory::findOne(['id' => $std['srd2_coadd_flag']]);
				$addWithGroup = $addHistory->ah_addWithGroup_relId;
				// $evtd = CCF::checkIfEvaluated($addWithGroup,$course->id,$evalueeId,Yii::$app->user->identity->id);
				$coff3 = CCF::getCourseInstructorForGroup($addWithGroup, $course->id);
				$grrrp = $addWithGroup;
			} else {
				$coff3 = CCF::getCourseInstructorForGroup($gset->id, $course->id);
				$grrrp = $gset->id;
			}
			if ($coff3) {
				if (is_null($coff3['coff2det_inst_relId'])) {
					$evalueeId = NULL;
				} else {
					// $evalueeId = \backend\models\Instructor::findOne(['id'=>$coff3['coff2det_inst_relId']])->inst_user_relId;
					$evalueeId = $coff3->coff2det_inst_relId && $coff3->coff2detInstRel ? $coff3->coff2detInstRel->inst_user_relId : null;

					$evalueeId2 = is_null($coff3['coff2det_TA_relId']) || (strtoupper($student->stdProgRel->program_name) == 'MASTERS') ? NULL : \backend\models\Instructor::findOne(['id' => $coff3['coff2det_TA_relId']])->inst_user_relId;
				}


				$evtd = CCF::checkIfEvaluated($grrrp, $course->id, $evalueeId, $stdUser->id);
				$evtd2 = is_null($evalueeId2) ? 1 : CCF::checkIfEvaluated($grrrp, $course->id, $evalueeId2, $stdUser->id);
				if (is_null($evtd) || is_null($evtd2)) {
					$evaltdAll = 0;
				}
			}

			$evaltdAll = (string) $evaltdAll;

			$gradeTable .= "<tr style='border-bottom: 1px solid black;'>
                  <td style='border: 1px solid black;'>" . ++$count . "</td>
                  <td style='text-align:center; border-right: 1px solid black; border-bottom: 1px solid black;'>" . $course->course_code . "</td>
              	  <td style='border-right: 1px solid black; border-bottom: 1px solid black;'>" . $course->course_title . "{$rptd}</td>
                  <td style='text-align:center; border-right: 1px solid black; border-bottom: 1px solid black;'>" . $course->course_crdthr . "</td>
                  <td style='text-align:center; border-right: 1px solid black; border-bottom: 1px solid black;'>" . $ltrgrd . "</td>
                  <td style='text-align:center; border-right: 1px solid black; border-bottom: 1px solid black;'>" . $grpt . "</td>
                  <td style='text-align:center; border-right: 1px solid black; border-bottom: 1px solid black;'>" . $caddda . "</td>
                  <td style='text-align:center; border-right: 1px solid black; border-bottom: 1px solid black;'  class='non-print'>" . $std['srd2_gradeApproved'] . "</td>
                  ";
			if (is_null($print)) {
				$gradeTable .= "<td style='text-align:center; border-right: 1px solid black; border-bottom: 1px solid black;'  class='non-print'>{$evaltdAll}</td>";
			}
			$gradeTable .= "</tr>";
		}
	} else {
		$gradeTable .= "{$gradeHistory}";
	}

	$srd2Model = new \backend\models\SemesterRegistration2Detail();
	// <input type='hidden' value='{$gsetId}' id='_gsetId' />
	$count1 = $count == 0 ? 'NO course ' : ($count == 1 ? $count . ' course ' : $count . ' courses ');
	$gradeTable .= "</tbody><tfoot style='background-color:#ddf;'>
                <tr>
                <td colspan=9 style='text-align:center;'>
                ";
	if (is_array($gradeHistory) && is_array($gradeHistory[1])) {

		$semesterStatusObject = NULL;
		// var_dump($gset);exit;
		// isset($gradeHistory[1][1][4]) && count($gradeHistory[1][1][4]) > 0
		if (isset($gset)) {
			$a = strtoupper($gset->gsProgRel->program_name) == 'MASTERS';
			// specialCase
			$sds1 = $srd2Model->getSemregRecord($student->id, $gset->gs_clslvl_relId, $gset->gs_sem_relId, $student->stdGroupReal, NULL, $a, $gset);
			// var_dump($sds1);exit;
			$satatus1 = $sds1[2];
			$satatus2 = $srd2Model->getSemregRecordPrev($student->id, $gset, NULL)[2];
		} else {
			$satatus1 = 'GROUP ERROR';
		}

		$s11 = is_numeric($gradeHistory[1][1][2]) ? $gradeHistory[1][1][2] * $gradeHistory[1][1][1] : '-';
		$rrrr = isset($gradeHistory[1][1][5]) && is_numeric($gradeHistory[1][1][5]) ? sprintf("%0.2f", round($gradeHistory[1][1][5], 2, PHP_ROUND_HALF_UP)) : '-';
		// $rrrr = is_numeric($gradeHistory[1][1][2])?round($s11,2,PHP_ROUND_HALF_UP):'-';
		// $rrrr = is_numeric($gradeHistory[1][1][0])?sprintf("%0.2f",):$gradeHistory[1][1][0];
		$eeee = is_numeric($gradeHistory[1][2][0]) ? sprintf("%0.2f", round($gradeHistory[1][2][0], 2, PHP_ROUND_HALF_UP)) : (sprintf("%0.2f", round(($gradeHistory[1][2][1] / $gradeHistory[1][2][2]), 2, PHP_ROUND_HALF_UP)));

		$ss33 = $gradeHistory[1][2][1];

		$ss33 = is_numeric($ss33) ? round($ss33 * 4) / 4 : $ss33;
		$s11 = is_numeric($s11) ? round($s11 * 4) / 4 : $s11;

		// for the case of rounding to nearest of .25 or .75 place,
		// $ss33 = is_numeric($ss33)?round($ss33 * 2) / 4:$ss33;
		// $s11 = is_numeric($s11)?round($s11 * 2) / 4:$s11;


		// 
		$ss11 = is_numeric($gradeHistory[1][1][2]) ? sprintf("%0.2f", round($gradeHistory[1][1][2], 2, PHP_ROUND_HALF_UP)) : ($gradeHistory[1][1][2] == 0 ? '-' : $gradeHistory[1][1][2]);
		$ss22 = is_numeric($gradeHistory[1][0][2]) ? sprintf("%0.2f", round($gradeHistory[1][0][2], 2, PHP_ROUND_HALF_UP)) : ($gradeHistory[1][0][2] == 0 ? '-' : $gradeHistory[1][0][2]);
		$ss21 = is_numeric($gradeHistory[1][1][1]) ? sprintf("%0.2f", round($gradeHistory[1][1][1], 2, PHP_ROUND_HALF_UP)) : $gradeHistory[1][1][1];
		$ss23 = is_numeric($gradeHistory[1][0][1]) ? sprintf("%0.2f", round($gradeHistory[1][0][1], 2, PHP_ROUND_HALF_UP)) : $gradeHistory[1][0][1];
		$ss24 = is_numeric($gradeHistory[1][0][0]) ? sprintf("%0.2f", round($gradeHistory[1][0][0], 2, PHP_ROUND_HALF_UP)) : $gradeHistory[1][0][0];
		$ss31 = is_numeric($gradeHistory[1][2][2]) ? sprintf("%0.2f", round($gradeHistory[1][2][2], 2, PHP_ROUND_HALF_UP)) : $gradeHistory[1][2][2];
		$ss33 = is_numeric($ss33) ? sprintf("%0.2f", round($ss33, 2, PHP_ROUND_HALF_UP)) : $ss33;
		$s11 = is_numeric($s11) ? sprintf("%0.2f", round($s11, 2, PHP_ROUND_HALF_UP)) : $s11;
		if ($std->srd2SemregRel->semreg_gradeCompleted) {
			$semreg = $std->srd2SemregRel;
			$ss31 = sprintf("%0.2f", $semreg->semreg_totalCrdtHr);
			$ss33 = sprintf("%0.2f", $semreg->semreg_gradePoint_sum);
			$eeee = sprintf("%0.2f", round($semreg->semreg_CGPA, 2, PHP_ROUND_HALF_UP));
		}
		// ". $gradeHistory[1][1][1] ."  =>Total grade point.
		$gradeTable .= "<table class='table table-sm table-condensed'>
                            <thead>
                                <th> </th>
                                <th style='text-align:center;'>Crdthr</th>
                                <th style='text-align:center;'>Grade point</th>
                                <th style='text-align:center;'>Grade point Avrg.</th>
                            </thead>
                            <tbody style='text-align:center;'>
                            	<tr>
                                    <td style='text-align:right;'>Previous Total / Commulative: &nbsp;&nbsp;</td>
                                    <td style='text-align: right; padding-right: 25px;'>" . $ss11 . "</td>
                                    <td style='text-align: right; padding-right: 25px;'>" . $rrrr . "</td>
                                    <td style='text-align: right; padding-right: 40px;'>" .  $ss21 . "</td>
                                </tr>
                            	<tr>
                                    <td style='text-align:right;'>This Semester: &nbsp;&nbsp;</td>
                                    <td style='text-align: right; padding-right: 25px;'>" . $ss22 . "</td>
                                    <td style='text-align: right; padding-right: 25px;'>" . $ss23 . "</td>
                                    <td style='text-align: right; padding-right: 40px;'>" . $ss24 . "</td>
                                </tr>
                            	<tr>
                            		<td style='text-align:right;'>Current Total/Commulative: &nbsp;&nbsp;</td>
                            		<td style='text-align: right; padding-right: 25px;'>" . $ss31 . "</td>
                            		<td style='text-align: right; padding-right: 25px;'>" . $ss33 . "</td>
                            		<td style='text-align: right; padding-right: 40px;'>" . $eeee . "</td>
                            	</tr>
                            	<tr><td></td><td>&nbsp;&nbsp;&nbsp;&nbsp;</td><td></td><td></td></tr>
                            	<tr><td colspan = 2>Prev. Status: <u><b>{$satatus2}</b></u></td><td colspan = 2>Current Status: <u><b>{$satatus1}</b></u></td></tr>
                        	</tbody>
                        </table>";
	} else {
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
        			</tfoot></table>";

	return  $gradeTable;
}
