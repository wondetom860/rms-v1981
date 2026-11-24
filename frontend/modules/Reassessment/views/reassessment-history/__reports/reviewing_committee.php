<?php

if (isset($model)) {
    $commitees = $model->reassessmentCommittees;
    if (Yii::$app->user->can('DepartmentHead') && isset($organize) && !isset($print) && (count($commitees) < 3) && ($model->ras_registrarApprove_status)) {
        $assignNewBtn = "<button class='btn btn-xs btn-info pull-right' onclick='assignCommitteee({$model->id});return false;'>Add</button>";
    } else {
        $assignNewBtn = "";
    }
    if ($commitees) {
        echo "<p class='bg-info'>{$assignNewBtn}</p>";
        echo "<table class='table table-condensed table-sm table-bordered table-hover' style='font-size:9pt;'>";
        echo "<thead style='background-color: #8D3;'>
                <th>#</th>
                <th>Instructor Name</th>
                <th>Date Assigned</th>
                <th>Signed/Accept</th>
                <th>Options</th>
            </thead><tbody>";
        $count = 0;
        $r = $model->getReassessorMarks()->andWhere(['IS NOT', 'ras_changed_to', NULL])->count() > 0;
        foreach ($commitees as $commitee) {
            $rmvBtn = '-';
            $date_assigned = date("dMY", $commitee->created_at);
            $accept = $commitee->approval_status ? 'Accepted' : null;
            if($accept == null && $r){
                if(Yii::$app->user->can('Instructor') || Yii::$app->user->can('DepartmentHead')){
                    if($commitee->instRel->inst_user_relId == Yii::$app->user->identity->id){
                        $accept = "<button class='btn btn-xs btn-warning' onclick='acceptMarkChange({$commitee->id});return false;'>Accept</button>";
                    }
                }
            }

            if (!$r) {
                $rmvBtn = ($commitee->approval_status || isset($print) || !Yii::$app->user->can('DepartmentHead')) ? "" : "<button class='btn btn-xs btn-warning pull-right' title='Remove from reviewing list' onclick='removeFromList({$commitee->id});return false;'>Remove</button>";
            }
            echo "<tr>
                    <td>" . ++$count . "</td>
                    <td>{$commitee->instRel->getFullName()}</td>
                    <td>{$date_assigned}</td>
                    <td>{$accept}</td>
                    <td>{$rmvBtn}</td>
            </tr>";
        }
        echo "</tbody></table>";
    } else {

        echo "<p class='bg-warning'>No committee assigned {$assignNewBtn}</p>";
    }
} else {
    echo "<p class='bg-danger'>Invalid request</p>";
}

?>

<script>
window.acceptMarkChange = (commitee_id) => {
    if (confirm('Accept mark update?')) {
        $.post('/Reassessment/reassessment-history/accept-mark-change',{commitee_id: commitee_id}).done((resp) => {
            resp == 1 ? (window.location.href = window.location) : (alert(resp));
        });
    }
}
window.removeFromList = (commitee_id) => {
    if (confirm('Are you sure to remove from the list?')) {
        $.post('/Reassessment/reassessment-committee/delete?id=' + commitee_id).done((resp) => {
            if (resp == 1) {
                typeof(loadMembers) !== 'undefined' ? loadMembers(): (window.location.href = window
                    .location);
            }
        });
    }
}
</script>