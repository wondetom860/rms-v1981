<?php

use backend\models\Instructor;
use frontend\modules\ExamSchedule\models\ExInvigilators;

if (isset($model)) {
    $dateTimeScehdules = $model->getExDateTimeSchedules()
        ->orderBy([
            'date' => SORT_ASC,
            'shift' => SORT_ASC,
        ])
        ->all();
    if ($dateTimeScehdules) {
        $sd = date('dMY', $model->start_date);
        $ed = date('dMY', $model->end_date);
        echo "<table class='table table-sm table-condensed table-bordered'>
            <caption style='caption-side: top; text-align: center;color: #000;'>
                <h4>
                    Exam Schedule Entry: {$model->acadYearRel->getAcHeader()} : <b>Staff Assignments</b><br>
                    <small>Start Date: {$sd} | End Date: {$ed}</small>
                </h4>
        </caption>
        ";
        echo "<thead>
                <th>Date</th>
                <th colspan=2>Staff Assignment</th>
                <th>Remark</th>
            </thead><tbody>";
        foreach ($dateTimeScehdules as $dateTimeScehdule) {
            // $allStaff = $dateTimeScehdule->getAllocatedStaffsInthisDateTimeSchedule();
            $invss = getInvigilators($dateTimeScehdule);
            $stdBys = getStandBys($dateTimeScehdule);
            $sups = getSupervisors($dateTimeScehdule);

            $rowSpan = $invss[0] + $stdBys[0] + $sups[0];
            echo "<tr>
                <td rowspan={$rowSpan} style='vertical-align: middle; padding-left: 13px;'>{$dateTimeScehdule->getDateScheduled()}</td>
                {$invss[1]}
                {$stdBys[1]}
                {$sups[1]}
            ";
        }
        echo "<tbody></table>";
    } else {
        echo "<p class='bg-warning'>Date time scehdule not set yet</p>";
    }
} else {
    echo "<p class='bg-danger'>Invalid request</p>";
}


function getStandBys($model)
{
    $standBys = $model->standbys ? json_decode($model->standbys, true) : [];
    if (!empty($standBys)) {
        $invs = "";
        $rowCount = 0;
        $invs .= "<td colspan=3 style='text-align: center; background-color: #9F8;'>StandBys</td></tr><tr>";
        foreach ($standBys as $staffId) {
            $stafff = Instructor::findOne(['inst_user_relId' => $staffId]);
            $invs .= "
                <td>{$stafff->getFullName()}</td>
                <td>-</td>
                <td></td>
                </tr>
            ";
            $rowCount++;
        }
        // echo "As Invigilator: " . count($asInvigilator);
    } else {
        return [1, "<td colspan=3><p class='bg-warning'>No Standby assigned.</p></td></tr>"];
    }

    return [$rowCount+1, $invs];
}
function getSupervisors($model)
{
    $supervisors = $model->supervisors ? json_decode($model->supervisors, true) : [];
    if (!empty($supervisors)) {
        $invs = "";
        $rowCount = 0;
        $invs .= "<td colspan=3 style='text-align: center; background-color: #9F8;'>Supervisors</td></tr><tr>";
        foreach ($supervisors as $staffId) {
            $stafff = Instructor::findOne(['inst_user_relId' => $staffId]);
            $invs .= "
                <td>{$stafff->getFullName()}</td>
                <td>-</td>
                <td></td>
                </tr>
            ";
            $rowCount++;
        }
        // echo "As Invigilator: " . count($asInvigilator);
    } else {
        return [1, "<td colspan=3><p class='bg-warning'>No Supervisor assigned.</p></td></tr>"];
    }

    return [$rowCount+1, $invs];
}

function getInvigilators($model) //$model = $dateTimeSchedule
{
    $invigilators = ExInvigilators::find()
        ->innerJoinWith("examRoomSlotRel")
        ->innerJoin('ex_date_time_schedule', 'ex_date_time_schedule.id=ex_exam_room_slot.date_time_slot_relId')
        ->where([
            'exam_schedule_entry_relId' => $model->exam_schedule_entry_relId,
            'ex_exam_room_slot.date_time_slot_relId' => $model->id
        ])
        ->orderBy([
            'date' => SORT_ASC,
            'shift' => SORT_ASC,
        ])
        ->all();
    $invs = "";
    $rowCount = 0;
    if ($invigilators) {
        $invs .= "<td colspan=3 style='text-align: center; background-color: #9F8;'>Invigilators</td></tr><tr>";
        foreach ($invigilators as $invigilator) {
            $stafff = Instructor::findOne(['inst_user_relId' => $invigilator->staff_relId]);
            $invs .= "
                <td>{$stafff->getFullName()}</td>
                <td>{$invigilator->examRoomSlotRel->classRoomRel->room}</td>
                <td></td>
                </tr>
            ";
            $rowCount++;
        }
        // echo "As Invigilator: " . count($asInvigilator);
    } else {
        return [1, "<td colspan=3><p class='bg-warning'>No invigilators assigned.</p></td></tr>"];
    }

    return [$rowCount+1, $invs];
}
