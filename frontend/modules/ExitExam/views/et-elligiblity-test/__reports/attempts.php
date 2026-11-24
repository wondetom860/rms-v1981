<?php

use yii\helpers\Html;

if (isset($model)) {
    $attempts = $model->etTestDetails;
    $model->getPassStatus();
    if ($attempts) {
        $tryBtn = '';
        if ($model->isEligibleForTest()) {
            $tryBtn = Html::a('Try Again', ['#'], ['class' => 'btn btn-primary btn-xs pull-right', 'onclick' => "tryAgain(1,{$model->semregRel->semreg_std_relId});return false;", 'title' => 'Try Again']);
        }
        echo "<table class='table table-bordered'>";
        echo "<caption style='text-align: center; font-weight: bold; color: #000;'><h4>Attempts{$tryBtn}</h4></caption>";
        echo "<thead>";
        echo "<tr>
                    <th>#</th>
                    <th>Academic Year</th>
                    <th>Score</th>
                    <th>Pass Point</th>
                    <th>Status</th>
                    <th>Option</th>
                </tr>
            </thead><tbody>";
        $count = 0;
        $option = "";
        $showOptios = (Yii::$app->user->can('Admin') || Yii::$app->user->can('Registrar')) ? true : false;
        foreach ($attempts as $attempt) {
            $passPoint = $attempt->getPassMark();
            $attempt->determinePassStatus();
            if ($showOptios) {
                if (Yii::$app->user->can('Registrar') && $attempt->mark_obtained != null) { // && $attempt->grade_accepted == 0
                    $option = Html::a($attempt->grade_accepted == 1 ? 'Cancel' : 'Approve', ['#', 'title' => 'Change approval status'], ['class' => 'btn btn-xs btn-warning pull-right', "onclick" => "changeApprovalStatus({$attempt->id});return false;"]);
                } else {
                    if ($attempt->mark_obtained == null || $attempt->grade_accepted == 0) {
                        $option = $attempt->mark_obtained == null ? Html::a('Delete', ['#'], ['title' => 'Delete this request', 'class' => 'btn btn-warning btn-xs pull-right', "onclick" => "deleteAttempt({$attempt->id});return false;"]) : '';
                        $option .= Html::a($attempt->mark_obtained == null ? 'Submit' : 'Change', ['#'], ['title' => 'Submit mark', 'class' => 'btn btn-primary btn-xs pull-right', "onclick" => "submitMark({$attempt->id});return false;"]);
                    }
                    // $option = Html::a('View', ['et-elligiblity-test/view', 'id' => $attempt->id], ['class' => 'btn btn-primary btn-xs']);
                }
                // $option = Html::a('View', ['et-elligiblity-test/view', 'id' => $attempt->id], ['class' => 'btn btn-primary btn-xs']);
            }
            $m_obt = "";
            if (Yii::$app->user->can('Student')) {
                $m_obt = ($attempt->mark_obtained == null || $attempt->grade_accepted != 1) ? 'N/A' : sprintf("%0.2f", $attempt->mark_obtained);
            } else {
                $m_obt = sprintf("%0.2f", $attempt->mark_obtained);
            }
            $status = $attempt->getPassStatus();
            echo "<tr>";
            echo "<td>" . ++$count . "</td>";
            echo "<td>{$attempt->acadYearRel->getAcHeader()}</td>";
            echo "<td>{$m_obt}</td>";
            echo "<td>{$passPoint}</td>";
            echo "<td>{$status}</td>";
            echo "<td>{$option}</td>";
            echo "</tr>";
        }

        echo "</tbody>";
        echo "</table>";
    }
} else {
    echo "<p class='bg-warning'>Invalid request</p>";
    return;
}

?>

<script>
    window.deleteAttempt = function(id) {
        var data = {
            id: id
        };
        if (confirm("Are you sure you want to delete this attempt?")) {
            $.post('/ExitExam/et-test-detail/delete?id=' + id, function(response) {
                if (response.status == 'success') {
                    alert('Attempt deleted successfully.');
                    location.reload();
                } else {
                    alert('Failed to delete the attempt.');
                }
            }, 'json');
        }
    }
    window.tryAgain = function(id, std_id) {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['et-elligiblity-test/create']) ?>',
            type: 'POST',
            data: {
                'EtElligiblityTest[reason]': 1,
                'student_id': std_id
            },
            dataType: 'html',
            success: function(data) {
                $('#modal').modal('show')
                    .find('.modal-content')
                    .html(data);
            }
        });
    }
    window.changeApprovalStatus = function(id) {
        var url = "<?= \yii\helpers\Url::to(['et-test-detail/change-approval-status']) ?>";
        var data = {
            id: id
        };
        if (confirm("Are you sure you want to change the approval status?")) {
            $.post(url, data, function(response) {
                if (response.status == 'success') {
                    alert('Approval status changed successfully.');
                    location.reload();
                } else {
                    alert('Failed to change the approval status.');
                }
            }, 'json');
        }
    }
    window.submitMark = function(id) {
        var url = "<?= \yii\helpers\Url::to(['et-test-detail/submit-mark']) ?>";
        var mark = Number(prompt("Enter the mark obtained"));
        var data = {
            id: id,
        };
        if (mark > 0) {
            data.mark = mark;
        } else {
            // alert('Invalid mark entered.');
            return;
        }
        if (confirm("Are you sure you want to submit the mark?")) {
            $.post(url, data, function(response) {
                if (response.status == 'success') {
                    // alert('Mark submitted successfully.');
                    location.reload();
                } else {
                    alert('Failed to submit the mark.');
                }
            }, 'json');
        }
    }
</script>