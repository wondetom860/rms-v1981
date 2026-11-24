<?php

use backend\models\Instructor;
// use common\models\User;
use yii\bootstrap\Html;

/** @var frontend\modules\ExamSchedule\models\ExDateTimeSchedule $model */
echo "<div class='row'><div class='col-md-12'>";
if (isset($model)) {
    $add = isset($addSupervisorBtn) && $addSupervisorBtn == true;
    echo $add ?
        Html::a('<i class="fa fa-plus"></i>', ['#'], ['class' => 'btn btn-xs btn-success pull-right', 'title' => 'Add Supervisor', 'onclick' => 'ShowAddSupervisorForm(' . $model->id . ');return false;']) :
        '';
    if (!$model->supervisors) {
        echo $add ? '<div class="alert alert-info">No Supervisor added yet.</div>' : '';
    } else {
        $supervisors = json_decode($model->supervisors, true);

        if (count($supervisors) > 0) {
            echo '<table class="table table-bordered table-striped">';
            echo '<tr style="background-color: #ADE;">
                    <th>#</th>
                    <th>Staff Name</th>
                    <th>Actions</th>
                </tr>';
            $count = 0;
            foreach ($supervisors as $supervisor) {
                $stdByUser = Instructor::findOne(['inst_user_relId' => $supervisor]);
                echo '<tr>';
                echo '<td>' . ++$count . '</td>';
                echo '<td>' . $stdByUser->getFullName() . '</td>';
                echo '<td>' . Html::a('<i class="fa fa-trash"></i>', ['#'], ['class' => 'btn btn-xs btn-danger', 'title' => 'Remove Supervisor', 'onclick' => 'RemoveSupervisor(' . $model->id . ',' . $supervisor . ');return false;']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo $add ?  '<div class="alert alert-info">No Supervisor added yet.</div>' : '';
        }
    }
    echo "</div></div>";
} else {
    echo '<div class="alert alert-info">Invalid request</div></div></div>';
    return;
}

?>

<script>
    window.RemoveSupervisor = (id, userId) => {
        if (confirm('Are you sure you want to remove this supervisor?')) {
            $.post('/ExamSchedule/ex-date-time-schedule/remove-supervisor', {
                id: id,
                userId: userId
            }).done(function(data) {
                if (data == 1) {
                    ShowAddSupervisorForm(id);
                }
            });
        }
    }
    window.ShowAddSupervisorForm = (id) => {
        $('#modal').find('.modal-header').html('Add Supervisor');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/ExamSchedule/ex-date-time-schedule/add-supervisor', {
            id: id
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
</script>