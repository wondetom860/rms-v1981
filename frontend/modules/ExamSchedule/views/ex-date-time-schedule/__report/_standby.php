<?php

use backend\models\Instructor;
use common\models\User;
use yii\bootstrap\Html;

/** @var frontend\modules\ExamSchedule\models\ExDateTimeSchedule $model */
echo "<div class='row'><div class='col-md-12'>";
if (isset($model)) {
    $add = isset($addStandbyBtn) && $addStandbyBtn == true;
    echo $add ?
        Html::a('<i class="fa fa-plus"></i>', ['#'], ['class' => 'btn btn-xs btn-success pull-right', 'title' => 'Add Standby', 'onclick' => 'ShowAddStandbyForm(' . $model->id . ');return false;']) :
        '';
    if (!$model->standbys) {
        echo $add ? '<div class="alert alert-info">No Standby added yet.</div>' : '';
    } else {
        $standbys = json_decode($model->standbys, true);

        if (count($standbys) > 0) {
            echo '<table class="table table-bordered table-striped">';
            echo '<tr style="background-color: #ACE;">
                    <th>#</th>
                    <th>Staff Name</th>
                    <th>Actions</th>
                </tr>';
            $count = 0;
            foreach ($standbys as $standby) {
                $stdByUser = Instructor::findOne(['inst_user_relId' => $standby]);
                echo '<tr>';
                echo '<td>' . ++$count . '</td>';
                echo '<td>' . $stdByUser->getFullName() . '</td>';
                echo '<td>' . Html::a('<i class="fa fa-trash"></i>', ['#'], ['class' => 'btn btn-xs btn-danger', 'title' => 'Remove Standby', 'onclick' => 'RemoveStandby(' . $model->id . ',' . $standby . ');return false;']) . '</td>';
                echo '</tr>';
            }
            echo '</table>';
        } else {
            echo $add ?  '<div class="alert alert-info">No Standby added yet.</div>' : '';
        }
    }
    echo "</div></div>";
} else {
    echo '<div class="alert alert-info">Invalid request</div></div></div>';
    return;
}

?>

<script>
    window.RemoveStandby = (id, userId) => {
        if (confirm('Are you sure you want to remove this standby?')) {
            $.post('/ExamSchedule/ex-date-time-schedule/remove-standby', {
                id: id,
                userId: userId
            }).done(function(data) {
                if (data == 1) {
                    ShowAddStandbyForm(id);
                }
            });
        }
    }
    window.ShowAddStandbyForm = (id) => {
        $('#modal').find('.modal-header').html('Add Standby');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/ExamSchedule/ex-date-time-schedule/add-standby', {
            id: id
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
</script>