<?php
// use yii\helpers\Html;

use backend\models\Attendance;
use backend\models\WGroupSetup;
use yii\helpers\Html;

/**
 * @var yii\web\View $this
 * @var backend\models\SemesterRegistration2Detail $srd2s
 */

$modleSrd2 = $srd2s;
$attendaceModel = new Attendance();
$model->semregStdRel->calculateGPAs();
?>

<div class="container-fluid table-responsive">
    <?php if ($modleSrd2) {
    ?>
        <table border="1" cellspacing="0" cellpadding="4" style="font-size: 9pt;">
            <caption style="text-align: center; font-style: bold;">
                <h3 class="page-header"> Student Semester Registration Detail</h3>
                <p>
                    Student Name: <u><?= $model->semregStdRel->getFullName() ?></u>
                    <br>Academic Year: <u><?= $model->semregAcadYearRel->sch_year_desc . "-" . $model->semregSemesterRel->semester_name ?></u> &nbsp;&nbsp;&nbsp;&nbsp;
                    Class Year: <u><?= $model->semregClslvlRel->getClslvlDisp() ?></u>
                    <br>Program: <u><?= $model->semregClslvlRel->clslvlProgramRel->program_name ?></u>
                </p>
            </caption>
            <thead>
                <tr style="background-color: #fdd; font-weight: bold;">
                    <td>#</td>
                    <td>Course Code</td>
                    <td>Course Title</td>
                    <td>Hours</td>
                    <td>Rptd.</td>
                    <td>Att.(%)|Hrs</td>
                    <td>TotalMark</td>
                    <td>Grade</td>
                    <td>Approval</td>
                </tr>
            </thead>
            <tbody>
                <?php
                $c = 0;
                foreach ($modleSrd2 as $srd2) {
                    $ltGrade = '';
                    $totalMark = '-';
                    $btnRmv = '';
                    $bgcolor = $srd2->srd2_requested_remove == 1 ? '#F99' : '';
                    if (Yii::$app->user->can('Student')) {
                        if ($srd2->evaluatedCourseInstructors()) {
                            $totalMark = $srd2->getTotalMark();
                            $ltGrade = $srd2->srd2_grade_relId ? ($srd2->srd2_gradeApproved ? $srd2->srd2GradeRel->gs_ltr_grade : 'NA') : '-';
                        } else {
                            $ltGrade = 'NE';
                        }
                    } else {
                        $totalMark = $srd2->getTotalMark();
                        $ltGrade = $srd2->srd2GradeRel ? $srd2->srd2GradeRel->gs_ltr_grade : '-';
                    }

                    $course = $srd2->srd2CourseRel;
                    $crf = $srd2->srd2_corptd_flag ? $srd2->srd2_corptd_flag : '';
                    $gset = $srd2->srd2_coadd_flag ? WGroupSetup::findOne($srd2->srd2_coadd_flag) : $srd2->srd2GroupRel;
                    $a_rep = $attendaceModel->getAttendanceReport2($model->semregStdRel, $course->id, $gset);
                    // $totalMark = is_numeric($totalMark) ? sprintf("%0.2f",$totalMark) : '-';
                    $attendance = is_numeric($a_rep[0]) ? (sprintf("%0.2f", 100 - $a_rep[0])."%") : '-';
                ?>
                    <tr style="background-color: <?= $bgcolor ?>;">
                        <td><?= ++$c ?></td>
                        <td><?= $course->course_code ?></td>
                        <td><?= $course->course_title ?></td>
                        <td><?= "[{$course->course_crdthr}-{$course->course_lecture_hour}-{$course->course_lab_hour}/{$course->course_tutorial_hour}]" ?>
                        </td>
                        <td style="text-align: right;"><?= $crf ?></td>
                        <td style="text-align: right;"><?= $attendance ?></td>
                        <td style="text-align: right;"><?= $totalMark ?></td>
                        <td style="text-align: center;"><?= $ltGrade ?></td>
                        <td style="text-align: center;"><?= $srd2->srd2_gradeApproved ?></td>
                    </tr>
                <?php
                } ?>
                <tr>
                    <td></td>
                    <td colspan="7">
                        <br>
                        <?php if (!Yii::$app->user->can('Student')): ?>
                            <p class="bg-warning pull-right" style="font-style: normal;">
                                SGPA: <b><u><?= sprintf("%0.3f", $model->semreg_SGPA) ?></u></b>&nbsp;&nbsp;&nbsp;
                                CGPA: <b><u><?= sprintf("%0.3f", $model->semreg_CGPA) ?></u></b>&nbsp;&nbsp;&nbsp;
                                <br>Semester Status: <b><u><?= $model->semesterLastSsnRel ? $model->semesterLastSsnRel->sssn_name : '' ?></u></b>
                            </p>
                        <?php else: ?>
                            <p class="bg-info" style="font-style: italic;">
                                NE : Instructor and/ TA is not evaluated.<br>
                                NA : Grade Not approved by course's department head.
                            </p>
                        <?php endif ?>
                    </td>
                </tr>
            </tbody>
        </table>
    <?php
    } else {
        echo "<p class='bg-warning'> NOT AVAILABLE</p>";
    } ?>
</div>