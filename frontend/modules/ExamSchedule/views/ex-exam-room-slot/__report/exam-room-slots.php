<?php

use yii\bootstrap\Html;

if (isset($model)) {
    $add = isset($addRoomSlotBtn) && $addRoomSlotBtn == true;
    echo $add ?
        Html::a('<i class="fa fa-plus"></i>', ['#'], ['class' => 'btn btn-xs btn-success pull-right', 'title' => 'Add Standby', 'onclick' => 'ShowManageExamRoomSlot(' . $model->id . ');return false;']) :
        '';
        echo '<div class="row">';
        echo '<div class="col-md-12">';
        echo '<table class="table table-bordered table-striped">';
        echo '<tr style="background-color: #CCE;">
                    <th>#</th>
                    <th>Exam. Room</th>
                    <th>Date</th>
                    <th>Schedule Time</th>
                    <th>Sections</th>
                    <th>Course(s)</th>
                    <th>Assigned</th>
                    <th>Actions</th>
                </tr>';
        $count = 0;
        foreach (
            $model->getExExamRoomSlots()
                ->orderBy('class_room_relId')
                ->all() as $examRoomSlot
        ) {
            $sections = $examRoomSlot->getSections();
            $course = $examRoomSlot->getCourseCode();
            // $instructor = $examRoomSlot->getCourseInstructor();
            $totalStudents = $examRoomSlot->getExExaminees()->count();
            echo '<tr>';
            echo '<td>' . ++$count . '</td>';
            echo '<td>' . $examRoomSlot->classRoomRel->room . '</td>';
            echo '<td>' . $examRoomSlot->dateTimeSlotRel->getDateScheduled() . '</td>';
            echo '<td>' . $examRoomSlot->dateTimeSlotRel->getScheduleTime() . ': '.$examRoomSlot->dateTimeSlotRel->shift.'</td>';
            echo "<td>{$sections}</td>";
            echo "<td>{$course}</td>";
            echo "<td style='text-align: center;'>{$totalStudents}</td>";
            echo '<td>' . Html::a('<i class="fa fa-pencil"></i>', ['/ExamSchedule/ex-exam-room-slot/view?id=' . $examRoomSlot->id], ['class' => 'btn btn-xs btn-danger', 'title' => 'Manage Room Slot']) . '</td>';;
            echo '</tr>';
        }
        echo '</table>';
        echo "</div></div>";
} else {
    echo "Invalid request";
    return;
}

?>

<script>
    window.ShowManageExamRoomSlot = (id) => {
        $('#modal').find('.modal-header').html('Manage Exam Room Slot');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/ExamSchedule/ex-exam-room-slot/add-exam-room-slot?date_time_schedule_id=' + id, function(data) {
            $('#modalContent').html(data);
        });
    }
    window.RemoveRoomSlot = (id, roomSlotId) => {
        if (confirm('Are you sure you want to remove this room slot?')) {
            $.post('/ExamSchedule/ex-exam-room-slot/remove-room-slot', {
                id: id,
                roomSlotId: roomSlotId
            }).done(function(data) {
                if (data == 1) {
                    ShowManageExamRoomSlot(id);
                }
            });
        }
    }
</script>