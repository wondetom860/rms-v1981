<?php
use kartik\date\DatePicker;
$this->title = "Academic Calendar";
// $this->title = Yii::t('rms', 'Active Programs');
// $this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Calendar'), 'url' => ['/AcademicCalendar/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="AcademicCalendar-default-index">
    <h3 style="background-color: #8DA;"><?= $this->title ?></h3>
    <div class="row" style="font-family: cursive; font-size: 9pt; background-color: white;">
        <div class="col-md-5">
            <h4>
                Academic Year <a class="btn btn-xs btn-warning pull-right" href="/AcademicCalendar/ac-academic-year/">Manage</a>
            </h4>
            <div class="container-fluid">
                <?= $this->render('../ac-academic-year/__report/index')  ?>
            </div>
        </div>
        <div class="col-md-7" id="active-programs-container" style="height: 400px; overflow-y: auto;">
            <h4>
                Programs, <span style="font-style: italic; font-size: 7pt;">List of department of programs, class year, specializations and other related information of specific academic year.</span> <a class="btn btn-xs btn-warning pull-right" href="/AcademicCalendar/ac-active-programs/">Manage</a>
            </h4>
        </div>
    </div>
</div>
<div class="AcademicCalendar-ac-program-schedule-index" style="border-top: 1px solid black;">
    <div class="row" style="background-color: white;">
        <center><h4>Program Scedule</h4></center>
        <a class="btn btn-xs btn-warning pull-right" href="/AcademicCalendar/ac-program-schedule/">Manage</a>
        <div class="col-md-12" id="activity-form-container">
            
        </div>
    </div>
</div>
<script type="text/javascript">

    __acadYearId = 0;
    window.loadActivityForm = (id) => {
        __acadYearId = id;
        $("#activity-form-container").html("Loading program schedule form...");
        $.post('/AcademicCalendar/ac-program-schedule/get-program-schedule',{ayId:id}).done((resp) => { $("#activity-form-container").html(resp); });
    }
    window.loadProgramForm = ()=>{
        window.location.href = "/AcademicCalendar/ac-active-programs/create?syid="+__acadYearId;
    }
    window.loadActivePrograms = (id) => {
        __acadYearId = id;
        $("#active-programs-container").html("Loading Programs...");
        $.post('/AcademicCalendar/ac-active-programs/get-active-programs',{ayId:id}).done((resp) => { $("#active-programs-container").html(resp); });
    }
</script>