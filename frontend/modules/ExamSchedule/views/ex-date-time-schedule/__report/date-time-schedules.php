<?php

/**
 * @var frontend\modules\ExamSchedule\models\ExExamScheduleEntry $model
 */


if (isset($model)) {
    $btnAddNew = "<button type='button' class='btn btn-primary btn-xs pull-right' data-toggle='modal' onclick='ShowCreateNewForm(); return false;' data-target='#addDateTimeScheduleModal'>
                    Add New
                </button>";
    $dateTimeSchedules = $model->getExDateTimeSchedules()->orderBy(['date' => SORT_ASC])->all();
    if ($dateTimeSchedules) {
        $btnPrint22 = "<button type='button' class='btn btn-info btn-xs pull-right' title='Print exam program schedule' style='margin: 0px 4px;' onclick='showPrintWindow({$model->id}); return false;'>
                    Print
                </button>";
        $btnPrint23 = "<button type='button' class='btn btn-warning btn-xs pull-right' title='Print staff schedule' style='margin: 0px 4px;' onclick='showPrintStaffWindow({$model->id}); return false;'>
                Print
            </button>";
        $sd = date('dMY', $model->start_date);
        $ed = date('dMY', $model->end_date);
        echo "<table class='table table-bordered table-striped'>";
        echo "<caption style='caption-side: top; text-align: center;color: #000;'>
                <h4>Exam Schedule Entry: {$model->acadYearRel->getAcHeader()}</h4>
                <h5>Start Date: {$sd} | End Date: {$ed}{$btnAddNew}{$btnPrint22}{$btnPrint23}</h5>
        </caption>";
        echo '<thead><tr>';
        echo '<th>Date</th>';
        echo '<th>Shift</th>';
        echo '<th>Start Time</th>';
        echo '<th>End Time</th>';
        echo '<th>Option</th>';
        echo '</tr></thead><tbody>';
        foreach ($dateTimeSchedules as $dateTimeSchedule) {
            $viewBtn = "<a class='btn btn-xs btn-info pull-right' href='/ExamSchedule/ex-date-time-schedule/view?id={$dateTimeSchedule->id}' style='margin: 0px 3px;'>View</a> ";
            $optnUpdate = " <a class='btn btn-xs btn-warning pull-right' href='/ExamSchedule/ex-date-time-schedule/update?id={$dateTimeSchedule->id}' style='margin: 0px 3px;'>Update</a> ";
            $d = date('D, dMY', $dateTimeSchedule->date);
            $st = date('h:i A', $dateTimeSchedule->start_time);
            $et = date('h:i A', $dateTimeSchedule->end_time);
            echo '<tr>';
            echo '<td>' . $d . '</td>';
            echo '<td>' . $dateTimeSchedule->shift . '</td>';
            echo '<td>' . $st . '</td>';
            echo '<td>' . $et . '</td>';
            echo '<td>' . $viewBtn . $optnUpdate . '</td>';
            echo '</tr>';
        }
        echo '</tbody></table>';
    } else {
        echo 'No data found';
    }
}

?>

<script>
    window.showPrintStaffWindow = (entry_id) => {
        myw2 = window.open(
            '/ExamSchedule/ex-exam-schedule-entry/print-staff-schedule?entry_id=' + entry_id,
            'title=Staff Schedule Report',
            'height=900,width=1000');
    }
    window.showPrintWindow = (entryId) => {
        // showing print window
        myw2 = window.open(
            '/ExamSchedule/ex-exam-schedule-entry/print-exam-schedule?entry_id=' + entryId,
            'title=Exam Schedule',
            'height=900,width=1000');
    }
    window.ShowCreateNewForm = () => {
        $('#modal').find('.modal-header').html('Add new Exam Date Time Schedule');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/ExamSchedule/ex-date-time-schedule/create', {}).done(function(data) {
            $('#modalContent').html(data);
        });
    }
</script>