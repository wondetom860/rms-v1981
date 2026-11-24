<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\models\Attendance */
$acadYear = $model->aGroupRel->gsAcadYearRel->sch_year_desc."-".$model->aGroupRel->gsSemRel->semester_name;
$this->title = 'Attendance Report - '.date_format(date_create($model->a_createdAt),"d/m/Y");
$this->params['breadcrumbs'][] = ['label' => 'Attendances', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attendance-view">

    <div class="container-fluid table-responsive">
        <div class="panel panel-info">
            <div class="panel-heading" style="font-size: 9pt;">
                <h3><?= Html::encode($this->title); ?><small class="pull-right" style="font-size: 10pt"><?= "<b>Course: </b>". $model->aCourseRel->course_title."<b> Group : </b>".$model->aGroupRel->gsGroupRel->group_name ?></small></h3>
            </div>
            <div class="panel-body">
                <div class="row">
                    <div class="col-md-3" style="text-align: center;">
                        <u><b>Academic Year</b></u><br><?= $acadYear ?>
                    </div>

                    <div class="col-md-3" style="text-align: center;">
                        <u><b>Instructor</b></u><br><?= $model->aInstructorRel->getFullName_av() ?>
                    </div>

                    <div class="col-md-3" style="text-align: center;">
                        <u><b>Course</b></u><br><?= $model->aCourseRel->course_title ?>
                    </div>

                    <div class="col-md-3" style="text-align: center;">
                        <u><b>Date</b></u><br><?= date_format(date_create($model->a_createdAt),'d/m/Y') ?>
                        <a class="btn btn-xs btn-warning btn-link pull-right" href="/attendance/update?id=<?= $model->id ?>">Update</a>
                    </div>
                </div>
                <hr>
                <div class="row">
                    <div class="col-md-6">
                        <h4 class="bg-warning" style="padding: 7px;">Absents</h4>
                        <div class="container-fluid" id="absents_cobtainer" style="font-size: 9pt;">
                            <?= $this->render('__report/absents',['model' => $model]) ?>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <h4 class="bg-success" style="padding: 7px;">Presents</h4>
                        <div class="container-fluid" id="presents_cobtainer" style="font-size: 9pt;">
                            <?= $this->render('__report/presents',['model' => $model]) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <script type="text/javascript">
        window.absentStudent = (stdId,attendanceEntry,mode=1) =>{
            if (mode == 1) {
                if (confirm('Student absent from the class?')) {
                    $.post('/attendance/absent-student',{attendanceEntry:attendanceEntry,stdId:stdId,mode:mode}).done((resp) => {
                        $("#absents_cobtainer").html(resp);
                        presentStudent(stdId,attendanceEntry,0);
                    });
                }
            }else {//wants to read list
                $.post('/attendance/absent-student',{attendanceEntry:attendanceEntry,stdId:stdId,mode:mode}).done((resp) => {
                    $("#absents_cobtainer").html(resp);
                });
            }
        }

        window.presentStudent = (stdId,attendanceEntry,mode=1) =>{
            if (mode == 1) {
                if (confirm('Student present in the class?')) {
                    $.post('/attendance/present-student',{attendanceEntry:attendanceEntry,stdId:stdId,mode:mode}).done((resp) => {
                        $("#presents_cobtainer").html(resp);
                        absentStudent(stdId,attendanceEntry,0);
                    });
                }
            }else{
                $.post('/attendance/present-student',{attendanceEntry:attendanceEntry,stdId:stdId,mode:mode}).done((resp) => {
                    $("#presents_cobtainer").html(resp);
                    // absentStudent(stdId,attendanceEntry,0);
                });
            }
        }
    </script>
</div>
