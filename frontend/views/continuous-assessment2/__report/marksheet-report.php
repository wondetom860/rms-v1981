<?php 

use backend\models\ContinuousAssessment2;
use backend\models\ContinuousAssessment2Search;
use \backend\models\ContAssessCatagory2;
use \backend\models\InstanceMarkAssignment;
use \frontend\models\SemesterRegistration2;
use \frontend\models\SemesterRegistration2Detail;

$table = '';
$contAssObj = new ContinuousAssessment2();
$ima = new InstanceMarkAssignment();

$coff2DetId = \backend\models\CourseOffering2Detail::findOne(['id' => $coffdetId]);

$courseId = $coff2DetId->coff2det_course_relId;
$acadYearId = $coff2DetId->coff2detCoff2Rel->coff2_acadYear_relId;
$semId = $coff2DetId->coff2detCoff2Rel->coff2_sem_relId;

if ($coff2DetId) {
    $gsetId = \backend\models\CourseOffering2::findOne(['id' => $coff2DetId->coff2det_coff2_relId])->coff2_group_relId;
} else {
    echo "<p class='bg-warning'>Invalid Orrefing record.</p>";
    die();
}


$srd = \frontend\models\SemesterRegistration2Detail::find()
        ->where(['srd2_course_relId' => $courseId, 'srd2_group_relId' => $gsetId,])
        ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
        ->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')
        ->innerJoin('user','user.id = student.std_user_relId')
        ->andWhere([
            'semreg_acadYear_relId' => $acadYearId, 
            'semreg_semester_relId' => $semId,
        ])
        ->with('srd2SemregRel')
        ->orderBy('user.first_name')
        ->all();


$gset = \backend\models\WGroupSetup::findOne(['id' => $gsetId]);
// Get inst groups from course offering detail.

// $dept = \backend\models\Department::findOne(['id' => $gset->gs_dept_relId])->dept_name;
// $clslvl = \backend\models\ClassLevel::findOne(['id' => $gset->gs_clslvl_relId])->clslvl_num;
$instNmae = $coff2DetId->coff2detInstRel->getFullName();
// $gname = \backend\models\WGroup::findOne(['id' => $gset->gs_group_relId])->group_name;
$course = $coff2DetId->coff2detCourseRel;

$acadyear = $gset->gsAcadYearRel->sch_year_desc.'-'.$gset->gsSemRel->semester_name;

if ($srd && count($srd) > 0) {

    $btnPrint = null;
    $headerText = "";
    // if (!$print) {
    //     $headerText = "<h2>".strtoupper(Yii::$app->params['client']).', '.Yii::$app->params['college']."</h2>";
    // }

    echo "<table class='table table-hover table-sm table-bordered table-condensed' style='font-size:9pt; border-color:#000;'>";
    echo "<caption style='text-align:center; font-size:10pt;'>
            {$headerText}
            <h3 style='text-decoration:underline; text-align:center;'>Mark Sheet Report</h3>
            Department:<u><i>{$gset->gsDeptRel->dept_name}</i></u>&nbsp;&nbsp;&nbsp;<br>
            Course Title:<u><i>{$course->course_title}</i></u>&nbsp;&nbsp;&nbsp;<br>
            Class Level:<u><i>{$gset->gsClslvlRel->clslvl_num}</i></u>&nbsp;&nbsp;&nbsp;
            Program:<u><i>{$gset->gsAdmRel->admission_name} {$gset->gsProgRel->program_name}</i></u>&nbsp;&nbsp;&nbsp;
            Group:<u><i>{$gset->gsGroupRel->group_name}</i></u>&nbsp;&nbsp;&nbsp;<br>
            Instructor Name : <u><i>{$instNmae}</i></u>&nbsp;&nbsp;&nbsp;
            Academic Year : <u><i>{$acadyear}</i></u>
        </caption>";
    // $mdCatagory = $catObj->getCourseCatagor($course->id,);

    // $instances = $contAssObj->getAssessmentInstances($course->id, $coffdetId);
    $instances = $ima->getAssessmentInstances2($course->id,NULL,$acadYearId,$semId);
    echo "<thead style='background-color:#ace;border-color:#000;'>
    	<tr style='background-color:#ace;border-color:#000;'>
        	<th style='border-color:#000;'>#</th>
        	<th colspan=2 style='text-align:center; border-color:black;'>Student Detail</th>
        	<th colspan=1 style='text-align:center; border-color:black;'>Crs Added</th>
        	<th colspan=" . (count($instances) + 2) . " style='text-align:center; border-color:#000;'> Assessment detail</th>
        </tr>
        <tr style='background-color:#ace; border-color:black;'>
            <th style='border-color:#000;'>#</th>
            <th style='border-color:#000;'>Reg. ID.</th>
            <th style='border-color:#000;'>Student Name</th>
            <th style='border-color:#000;'>Crs. Added</th>
            ";
    // get assessment catagries (sub_catagories if any) made for the course and append them as the headr.

    if ($instances && count($instances) > 0) {
        $namesArray = [];

        foreach ($instances as $key) {
            // display actual instances of assessment under the category.
            // $catagoryName = \backend\models\ContAssessCatagoryName::findOne(['id'=>$key->cat_name_relId])->cont_cat_name;
            $instanceName = InstanceMarkAssignment::findOne($key->id);
            if (!in_array($instanceName->ima_instanceName, $namesArray)) {
                $namesArray[] = $instanceName->ima_instanceName;
                 // check if is course Instructor::
                $ppp = '';
                if (Yii::$app->user->can('Instructor') || Yii::$app->user->can('DepartmentHead')) {
                    $inst = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id]);

                    if (!is_null($coff2DetId->coff2det_inst_relId) && $coff2DetId->coff2det_inst_relId == $inst->id) {
                        $ppp = "<p class='btn btn-link btn-xs' onclick='LoadModalForEdit({$key->id},{$gsetId},{$course->id})' title='Click here to change student mark entered under this instance.'>" . $instanceName->ima_instanceName . "(" . $instanceName->ima_possiblePoint . "%)</p>";
                    }else{
                        $ppp = $instanceName->ima_instanceName . "(" . $instanceName->ima_possiblePoint."%)";
                    }
                }else{
                    $ppp = $instanceName->ima_instanceName . "(" . $instanceName->ima_possiblePoint."%)";
                    }
                echo "<th style='text-align:center; border-color:#000;'>{$ppp}</th>";
            }
        }
    } else {
        echo "<th colspan=" . (count($instances) - 1) . "><p class='bg-danger'>No mark submitted for this course yet.</p></th>";
    }

    echo "<th style='text-align:center;border-color:#000;'>Total.</th>
            <th style='border-color:#000;'>Grade</th>
         </tr></thead><tbody>";
    $count = 0;
    foreach ($srd as $std) {

        $s = \backend\models\Student::findOne(['id' => $std['srd2SemregRel']['semreg_std_relId']]);
        $u = \backend\models\User::findOne(['id' => $s->std_user_relId]);

        $gid = $std->srd2_grade_relId;
        //$grade = is_null($gid)?'-':GradeScale::findOne(['id'=>$gid])->gs_ltr_grade;
        $caddda = is_null($std->srd2_coadd_flag) ? 'NO' : 'YES';
        echo "<tr>
              <td style='border-color:#000;'>" . ++$count . "</td>
              <td style='border-color:#000;'>" . $s->std_regId . "</td>
              <td style='border-color:#000;'>" . $u->first_name . ' ' . $u->middle_name . "</td>
              <td style='border-color:#000;'>" . $caddda . "</td>
             ";
        $totalAssigned = 0;
        $totalgained = 0;
        $grade = 'N/C';
        $jumpedAssessment = false;
        $ttt = 'Mark Submission NOT COMPLETED yet.';
        foreach ($instances as $key2) {
            // For each assessment catagory, get the mark obtained by the student.
            $mark = '-';
            $instanceName = InstanceMarkAssignment::findOne($key2->id);
            $mark = $contAssObj->getStudentMark($courseId, $s->id, $key2->ima_catagorySetup_relId, $instanceName->id);
            if (($instanceName->ima_assessed == 1)) { //assessed and but have no valid mark
                if ($mark) {
                    $markDtae = $mark->ca_created_at;
                    $mark = $mark->ca_markObtained;
                    $totalgained += $mark;
                    $asessDate = date_format(date_create($markDtae), 'D, d M Y');
                    echo "<td style='text-align:center; border-color:#000;' title='{$asessDate}'>" . $mark . "</td>";
                }else{
                    $jumpedAssessment = true;
                    echo "<td style='text-align:center; border-color:#000; background-color:yellow;' title='Missed'>-</td>";
                }
                $totalAssigned += $instanceName->ima_possiblePoint;
            }else {
                echo "<td style='text-align:center; border-color:#000; background-color:red;' title='Not Assessed yet.'>-</td>";
            }
        }
        // $icon = '';
        echo "<td style='text-align:right; font-weight:bold; border-color:#000; background-color:#dff'>{$totalgained}/{$totalAssigned}</td>";

        if ($totalAssigned >= 100) {
            // Indicates the mark submission is completed. Ready to calculate LetterGrade depending on the grade scale.
            $ttt = "Equivalent Letter grade";
            $gradeObj = $contAssObj->setLetterGrade($totalAssigned, $totalgained, $std['srd2SemregRel']['semreg_std_relId'], $coffdetId, $std['id'],$jumpedAssessment);

            if ($gradeObj && !is_string($gradeObj)) {
                $grade = $gradeObj->gs_ltr_grade;
            }else{
                $grade = $gradeObj;
            }
            // add print option if the grade is calculated
            if (is_null($btnPrint) && $print) {
                $btnPrint = "<button class='btn btn-xs btn-warning pull-right' title='Print this mark sheet' onclick='markSheet({$coffdetId});return false;'>Print</button>";
            }
        }
        echo  "<td style='text-align:center; background-color:#cff;border-color:#000;' title='{$ttt}'>{$grade}</td></tr>";
    }
    echo "</tbody></table>";
    $count = $count == 0 ? 'NO Result ' : ($count == 1 ? $count . ' Result ' : $count . ' Results ');
    echo "<p class='bg-success'><i><b>" . $count . "</b></i>.{$btnPrint}</p>";
    echo $table;
    // die();
} else {
    echo "<p class='bg-warning'>No student enrolled this course in this semester.</p>";
}
?>