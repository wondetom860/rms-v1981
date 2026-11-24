<?php

use backend\models\Instructor;

if (isset($model)) {
    $examiness = $model->getExExaminees()->orderBy([
        'offering_detail_relId' => SORT_DESC
    ])->all();
    echo "<div class='row bg-info'>
            <div class='col-md-12' style='text-align:center;'>
                <h3>Attendance Sheet</h3>
                <p>
                    Exam Schedule Entry: <b><u>{$model->dateTimeSlotRel->getHeaderText()}</u></b> &nbsp;&nbsp;
                    Exam Room: <b><u>{$model->classRoomRel->room}</u></b> &nbsp;&nbsp;
                    Date: <b><u>" . $model->dateTimeSlotRel->getDateScheduled() . '</u></b> &nbsp;&nbsp;
                </p>
            </div>
        </div>';
    echo "<h4 style='text-align:center;'>Students</h4>";
    if ($examiness) {
        echo "<table class='table table-bordered table-striped' style='width:100%; font-size: 9pt;'>";
        echo "<tr style='background-color:#ACE;'>
                <th>#</th>
                <th>Reg. ID</th>
                <th>Student Name</th>
                <th>Section</th>
                <th>Course Code</th>
                <th>Phone</th>
                <th>Attendance</th>
                <th>Signature</th>
                <th>Remark</th>
                </tr><tbody>";
        $count = 0;
        $invigilators = getInvigiators($model);
        foreach ($examiness as $examinee) {
            $std = $examinee->stdRel;
            $acp = $examinee->offeringDetailRel->coff2detAcProgRel;
            $btnOption = '';
            $course = $examinee->offeringDetailRel->coff2detCourseRel;
            $attendance = $examinee->offeringDetailRel->getAttendaceReport($std);
            // $assigned = $model->assigned($std->id);
            // $clslll = $std->stdClslvlRel;
            echo '<tr>
                    <td>' . ++$count . "</td>
                    <td>{$std->std_regId}</td>
                    <td>{$std->getFullName()}</td>
                    <td>{$std->stdGroupRel->getGsDisp()}</td>
                    <td>{$course->getCourseCredits()}</td>
                    <td>{$std->stdUserRel->phone_mobile}</td>
                    <td style='text-align: center'>{$attendance}%</td>
                    <td style='text-align: center'>________________</td>
                    <td style='text-align: center'></td>
                </tr>";
        }
        echo '<tr style="background-color: #ACE;">
            <td colspan=7 style="text-align:center;">Total: ' . $count . ' students</td>
        </tr>
        ';
        echo '</tbody></table>';
        echo "<div class='row' style='padding-left: 30px;'>
                <div class='col-md-12'>{$invigilators}</div>
            </div>";
    } else {
        echo 'No student assigned.';
    }
}

function getInvigiators($model)
{
    $invigilators = $model->exInvigilators;
    $resp = '';
    if ($invigilators) {
        $resp .= '<table style="width: 98%" class="table table-sm table-condensed">
                <tr>
                    <th>Invigilator</th>
                    <th>Signature</th>
                    <th>Date</th>
                    <th>Remark</th>
                </tr>';
        foreach ($invigilators as $ex) {
            $dt = date('d/m/Y',$ex->examRoomSlotRel->dateTimeSlotRel->date);
            $inst = Instructor::findOne(['inst_user_relId' => $ex->staff_relId]);
            $resp .= "<tr>
                <td>{$inst->getFullName()}</td>
                <td>________________</td>
                <td>{$dt}</td>
                <td></td>
            </tr>";
        }
        $resp .= '</table>';
    }

    return $resp;
}

?>