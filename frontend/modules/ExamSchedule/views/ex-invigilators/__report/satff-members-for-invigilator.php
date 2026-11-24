<?php

/**
 * @var frontend\modules\ExamSchedule\models\ExInvigilators $model
 */

use backend\models\Instructor;
use common\models\User;

$allocatedForThisdateTimeSchedule = $model->getAllocatedStaffsInthisDateTimeSchedule();
$scheduledCourseInstructors = $model->getScheduledCourseInstructors();

if (isset($model)) {
    $allUsers = Instructor::find()
        ->innerJoinWith('instUserRel')
        ->where([
            'user.status' => User::STATUS_ACTIVE,
            'user.activated' => 1
        ])
        ->andWhere(['not in', 'inst_user_relId', $allocatedForThisdateTimeSchedule])
        ->andWhere(['not in', 'inst_user_relId', $scheduledCourseInstructors])
        ->orderBy([
            'inst_dep_relId' => SORT_ASC,
            'first_name' => SORT_ASC,
            'middle_name' => SORT_ASC,
            'last_name' => SORT_ASC
        ])
        ->all();
    if ($allUsers) {
        echo "<div style='max-height: 400px; overflow: auto; padding: 3px;'>";
        echo "<table class='table table-sm table-condensed table-bordered table-hover'>";
        echo "<tr style='background-color: #f98;'>
            <th>#</th>
            <th>Staff Name</th>
            <th>Type</th>
            <th>Employment</th>
            <th>Department</th>
            <th>Recent Assignments</th>
            <th>Option</th>
        </tr>";
        $count = 0;
        foreach ($allUsers as $inst) {
            $user = $inst->instUserRel;
            $empType = $inst->employmentTypeRel ? $inst->employmentTypeRel->et_name : "Uknown";
            $recentAssignment = $model->getRecentExamSchedule($user->id, 1);
            $option = "<input type='checkbox' id='chkbx_001_{$user->id}' onchange='addStaffAsInvigilator({$model->exam_room_slot_relId},{$user->id}); return false;' ";
            echo "<tr>
                <td>" . ++$count . "</td>
                <td>{$inst->getFullName()}</td>
                <td>{$user->user_type}</td>
                <td>{$empType}</td>
                <td style='text-align: center;'>{$inst->instDepRel->dept_code}</td>
                <td>{$recentAssignment}</td>
                <td style='text-align: center;'>{$option}</td>
            </tr>";
        }
        echo "</table></div>";
    } else {
        echo "Staff List Not Found";
    }
} else {
    echo "Invalid request";
}


?>

<script>
    __selectedsArr = [];
    window.addStaffAsInvigilator = (roomSlotId, staffId) => {
        let chk = document.getElementById(`chkbx_001_${staffId}`);
        let selecteds = document.getElementById('selecteds_001');
        if (chk.checked) {
            __selectedsArr.push(staffId);
        } else {
            __selectedsArr = __selectedsArr.filter((item) => item !== staffId);
        }
        selecteds.innerText = __selectedsArr.length;
        $("#exinvigilators-staffs").val(JSON.stringify(__selectedsArr));
        // console.log(__selectedsArr);
    }
</script>