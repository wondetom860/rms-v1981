<?php

use yii\bootstrap\Html;

if (isset($model)) {
    $add = isset($addOfferingDetailBtn) && $addOfferingDetailBtn == true;
    $offerigns = is_null($model->offering_detail_IDs) ? null : json_decode(json_encode($model->offering_detail_IDs), true);
    $btn = $add
        ? Html::a('<i class="fa fa-plus"></i>', ['#'], ['class' => 'btn btn-xs btn-success pull-right', 'title' => 'Add Offerings/Courses', 'onclick' => 'ShowManageExamOfferingDetail(' . $model->id . ');return false;'])
        : '';
    if (is_null($offerigns)) {
        echo "<p class=' alert alert-warning'>No course selected{$btn}</p>";
        // return;
    } else {
        echo $btn;
        echo '<div class="row">';
        echo '<div class="col-md-12">';
        echo "<h4 style='text-align: center;'>Allocated Courses to this Exam Room Slot</h4>";
        echo '<table class="table table-bordered table-striped" style="font-size: 9pt;">';
        echo '<tr style="background-color: #CCE;">
                    <th>#</th>
                    <th>Course Dept.</th>
                    <th>Course Credit</th>
                    <th>Title</th>
                    <th>Section Enrolled</th>
                    <th>Instructor</th>
                    <th># of Students</th>
                    <th>Allocated</th>
                    <th>Remaining</th>
                    <th>Option</th>
                </tr>';
        $count = 0;
        $totalEnrolled = 0;
        $totalAllocated = 0;
        $availableSeats = $model->classRoomRel->num_of_seats;
        $btnPrintAttendance = Html::a('<i class="fa fa-list"></i>', ['#'], ['class' => 'btn btn-xs btn-info btn-block', 'title' => 'PrintAttendance Sheet for this Examin room', 'onclick' => "printAtttendance({$model->id});return false;"]);
        foreach ($model->getExExamOfferingDetails()->all() as $examOfferingDetail) {
            $course = $examOfferingDetail->coff2detCourseRel;
            $gset = $examOfferingDetail->coff2detCoff2Rel->coff2GroupRel;
            $instructor = $examOfferingDetail->coff2detInstRel ? $examOfferingDetail->coff2detInstRel->getFullName() : 'TBA';
            $sections = $gset->getGsDisp();
            $group = $gset->gsGroupRel->group_name;
            $studentsCount = $examOfferingDetail->getEnrollsCount();
            $allocated = $model->getAllocatedStudentByOfferingDetailId($examOfferingDetail->id);
            $allocated = $allocated ? count($allocated) : 0;
            $tt = $allocated == 0 ? '' : "<span class='label bg-primary'>Allocated</span>";
            $btnDelete = $tt . Html::a('<i class="fa fa-trash"></i>', ['#'], ['class' => 'btn btn-xs btn-danger', 'title' => 'Remove this course offering', 'onclick' => "removeOffering({$model->id},{$examOfferingDetail->id});return false;"]);
            $btnAllocate = ($studentsCount > $allocated)
                ? Html::a('<i class="fa fa-plus"></i>', ['#'], [
                    'class' => 'btn btn-xs btn-primary',
                    'title' => 'Place students to this exam room',
                    'onclick' => "addStudents({$model->id},{$examOfferingDetail->id});return false;"
                ])
                : "<span class='label bg-primary'>Done</span>";
            $totalEnrolled += $studentsCount;
            $totalAllocated += $allocated;

            $remaining = $studentsCount - $allocated;

            echo '<tr>';
            echo '<td>' . ++$count . '</td>';
            echo '<td>' . $course->courseDeptRel->dept_code . '</td>';
            echo '<td>' . $course->getCourseCredits() . '</td>';
            echo "<td>{$course->course_title}</td>";
            echo "<td>{$sections}<b>[{$group}]</b></td>";
            echo "<td>{$instructor}</td>";
            echo "<td style='text-align: center;'>{$studentsCount}</td>";
            echo "<td style='text-align: center;'>{$allocated}</td>";
            echo "<td style='text-align: center;'>{$remaining}</td>";
            echo "<td>{$btnAllocate} | {$btnDelete}</td>";
            echo '</tr>';
        }
        $totalRemaining = $totalEnrolled - $totalAllocated;
        echo "<tr style=' background-color: #ACE; font-weight: bold;'>
                <td colspan=6 style='text-align: center;'>Total</td>
                <td style='text-align: center;'>{$totalEnrolled}</td>
                <td style='text-align: center;'>{$totalAllocated}/{$availableSeats}</td>
                <td style='text-align: center;'>{$totalRemaining}</td>
                <td style='text-align: center;'>{$btnPrintAttendance}</td>
        </tr>";
        echo '</table>';
        echo '</div></div>';
    }
} else {
    echo 'Invalid request';
    return;
}

?>

<script>
    window.printAtttendance = (exam_room_slot_id) => {
        myw2 = window.open(
            '/ExamSchedule/ex-exam-room-slot/print-attendance-sheet?exam_room_slot_id=' + exam_room_slot_id,
            'title=Students assigned to this exam room',
            'height=900,width=1000');

        // $('#modal').find('.modal-header').html('Students assigned to this exam room');
        // $('#modalContent').css({
        //     'text-align': 'left',
        //     'margin-top': '0px'
        // });
        // $('#modalContent').html('Loading form...');
        // $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        // $('#modal').modal('show');
        // $.get('/ExamSchedule/ex-exam-room-slot/print-attendance-sheet?exam_room_slot_id=' + exam_room_slot_id,{}, function(data) {
        //     $('#modalContent').html(data);
        // });

        // window.open('/ExamSchedule/ex-exam-room-slot/print-attendance-sheet?exam_room_slot_id=' + exam_room_slot_id, '_blank');
    }
    window.addStudents = (exromId, offeringDetailId) => {
        $('#modal').find('.modal-header').html('Assign students to this exam room');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.post('/ExamSchedule/ex-examinees/create2', {
            exromid: exromId,
            offeringDetailIi: offeringDetailId
        }, function(data) {
            $('#modalContent').html(data);
        });
    }
    window.ShowManageExamOfferingDetail = (id) => {
        $('#modal').find('.modal-header').html('Manage Exam Room Slot (Add/Remove Offerings)');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/ExamSchedule/ex-exam-room-slot/add-offerings?id=' + id, function(data) {
            $('#modalContent').html(data);
        });
    }
    window.removeOffering = (id, OfferingDetailId) => {
        if (confirm('You want to remove this offering from the list?')) {
            $.post('/ExamSchedule/ex-exam-room-slot/remove-offering', {
                id: id,
                OfferingDetailId: OfferingDetailId
            }).done(function(data) {
                if (data == 1) {
                    ShowManageExamOfferingDetail(id);
                }
            });
        }
    }
</script>