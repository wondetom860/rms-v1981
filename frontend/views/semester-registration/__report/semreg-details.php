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

<div class="row" style="border-bottom: 1px solid black;">
    <div class="col-xs-12" style="text-align: center">
        <h5 class="page-header bg-info"> Semester Registration Detail</h5>
        <p>
            Student Name: <u><?= $model->semregStdRel->getFullName() ?></u>
            <br>Academic Year: <u><?= $model->semregAcadYearRel->sch_year_desc . "-" . $model->semregSemesterRel->semester_name ?></u> &nbsp;&nbsp;&nbsp;&nbsp;
            Class Year: <u><?= $model->semregClslvlRel->getClslvlDisp() ?></u>
        </p>
    </div><!-- /.col -->
</div>

<div class="container-fluid table-responsive">
    <?php if ($modleSrd2) {
    ?>
        <table class="table table-sm table-condensed table-bordered table-hover" style="font-size: 9pt;">
            <thead style="background-color: #DFD;">
                <th>#</th>
                <th>Course Code</th>
                <th>Course Title</th>
                <th>Hours</th>
                <th>Rptd.</th>
                <th>Att.(%)|Hrs</th>
                <th>TotalMark</th>
                <th>Grade</th>
                <th>Approval</th>
                <th>Option</th>
            </thead>
            <tbody>
                <?php
                $c = 0;
                foreach ($modleSrd2 as $srd2) {
                    $ltGrade = '';
                    $totalMark = '-';
                    $btnRmv = '';
                    $bgcolor = $srd2->srd2_requested_remove == 1 ? '#F99' : '';
                    if (Yii::$app->user->can('Admin') && $srd2->srd2_gradeLocked != 1 && $srd2->srd2_gradeApproved != 1 && $srd2->srd2_requested_remove != 1) {
                        $btnRmv = Html::a('<i class="fa fa-trash"></i>', ['removed-srd2/create', 'srd2Id' => $srd2->id], [
                            'class' => 'btn btn-danger btn-xs',
                            'title' => 'Remove this course from semester registration',
                            'data' => [
                                'confirm' => 'Are you sure you want to remove this course from semester registration?',
                                'method' => 'post',
                            ],
                        ]);
                    }
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
                    $attendance = is_numeric($a_rep[0]) ? sprintf("%0.2f", 100 - $a_rep[0]) : '-';
                    $attendance = "{$attendance}|{$a_rep[1]}";
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
                        <td style="text-align: right;"><?= $btnRmv ?></td>
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