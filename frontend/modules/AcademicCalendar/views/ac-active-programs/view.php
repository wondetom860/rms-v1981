<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;

/** @var yii\web\View  $this
 * @var frontend\modules\AcademicCalendar\models\AcActivePrograms  $model
 */

$this->title = $model->academicYearRel->getAcHeader();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Calendar'), 'url' => ['/AcademicCalendar/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Programs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$rr = '';
?>
<div class="ac-active-programs-view container-fluid">

    <h3><?= Html::encode($this->title) ?>
        <?php if (\Yii::$app->user->can('Admin') && $model->updelPossible()) { ?>

            <p class="pull-right">
                <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
                <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
                    'class' => 'btn btn-danger',
                    'data' => [
                        'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                        'method' => 'post',
                    ],
                ]) ?>
            </p>
        <?php } ?>
    </h3>

    <div class="row" style="background-color: white;">
        <div class="col-md-5" style="padding: 0px; background-color: darkgrey;">
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [

                    [
                        'attribute' => 'admission_relId',
                        'value' => function ($model) {
                            return $model->admissionRel->admission_name . "-" . $model->programRel->program_name;
                        }
                    ],
                    [
                        'attribute' => 'dept_relId',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->deptRel->dept_name;
                        }
                    ],
                    [
                        'attribute' => 'class_level_relId',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->classLevelRel->getClslvlDisp();
                        }
                    ],
                    [
                        'attribute' => 'semester_relId',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->semesterRel ? $model->semesterRel->semester_name : "-";
                        }
                    ],
                    [
                        'attribute' => 'spec_relId',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->specRel ? $model->specRel->spec_name : "-";
                        }
                    ],
                    [
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->status ? "Active" : "Not Active";
                        }
                    ],
                    [
                        'attribute' => 'graduate_class',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $chkd = $model->graduate_class ? "checked" : "";
                            $btn = $model->graduate_class ? 
                                "<button class='btn btn-xs pull-right btn-primary' style='margin: 0px 3px;' onclick='printPointHistory($model->id);return false;' title='print point history of this section'>Point History</button>" : 
                                '';
                            $btn2 = $model->graduate_class ? 
                                "<button class='btn btn-xs pull-right btn-info' style='margin: 0px 3px;' onclick='pringGCList2({$model->id});return false;' title='Show GC Students Grade Summery'>Show List</button>" : 
                                '';
                            return "<div class='form-check form-switch pull-left'>
                                <input class='form-check-input pull-left' onchange='changeGcStatus({$model->id});return false;' type='checkbox' id='flexSwitchCheckChecked' {$chkd}>
                                </div>{$btn}{$btn2}";
                            // return $model->graduate_class? "Graduate Class" : "Non-Graduate Class";
                        }
                    ],
                    [
                        'attribute' => 'on_internship',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $onInt = $model->on_internship ? "checked" : "";
                            return "<div class='form-check form-switch pull-left'>
                                <input class='form-check-input pull-left' onchange='changeInternshipStatus({$model->id});return false;' type='checkbox' id='flexSwitchCheckChecked2' {$onInt}>
                            </div>";
                            // return $model->on_internship ? "On Internship" : "Non-Internship";
                        }
                    ],
                    [
                        'attribute' => 'entry_type',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $onInt = $model->entry_type ? "checked" : "";
                            $status = $model->entry_type == 1 ? 'REGULAR' : ($model->entry_type == 2 ? 'TRANSFER' : 'UKNOWN');
                            return "<div class='form-check form-switch pull-left'>
                                ({$status})<input class='form-check-input pull-left' onchange='changeEntryType({$model->id});return false;' type='checkbox' id='flexSwitchCheckChecked2' {$onInt}>
                            </div>";
                            // return $model->on_internship ? "On Internship" : "Non-Internship";
                        }
                    ],
                    'remark:ntext',
                    [
                        'attribute' => 'group_setups',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $this->render('../../../../views/w-group-setup/__report/wgroup_setups_per_acprogram', ['gsModels' => $model->groupSetups]);
                        }
                    ],
                    [
                        'attribute' => 'course_offerings',
                        'format' => 'raw',
                        'value' => function ($model) {
                            $courses = $model->getCourseOffered();
                            if (count($courses) > 0) {
                                // $rr = 
                                return count($courses) . " Course(s) Offered. <button class='btn btn-xs btn-primary pull-right' onclick='showCourses();return false;'>Show</button>";
                            } else {
                                return "<p class='warning'>-</p>";
                            }
                        }
                    ],
                ],
            ]) ?>
        </div>

        <!-- div lists academic programs in the selected academic year -->
        <div class="col-md-7" style="font-family: cursive; font-size: 9pt;">
            <h5>Program Schedule form : <button class="btn btn-xs btn-warning" onclick="$('.ac-program-schedule-form').toggle('display'); $(this).text($(this).text() == '+' ? '-' : '+'); return false;">+</button></h5>
            <?= $this->render('../ac-program-schedule/__report/program-schedules', ['schedules' => $model->getAcProgramSchedules()->orderBy(['end_date' => SORT_DESC])->all(), 'pid' => $model->id]) ?>
        </div>
        <?php //if ($model->status != $model::STATUS_CLOSED) { 
        ?>

        <?php //} else {
        //echo "<p class='bg-warning'>Program NOT active</p>";
        //} 
        ?>
    </div>

</div>
<script type="text/javascript">
    // changeInternshipStatus
    const pringGCList2 = function(acyId) {
        myw2 = window.open(
            '/AcademicCalendar/ac-active-programs/graduate-student-list2?acpId=' + acyId + "&gsetId=" + 1939 +"&top=" + 31,
            'title=Graduate Student List Report 2',
            'height=1100,width=1800');
    }
    const printPointHistory = (acpId) => {
        myw5 = window.open(
			'/semester-registration/print-point-history?acpId=' + acpId,
			'title=Graduate Class Stidents\' Point History',
			'height=900,width=1100');
    }
    const changeEntryType = (acpId) => {
        if (confirm('Are you sure to change entry type of this section/program?')) {
            $.post('/AcademicCalendar/ac-active-programs/change-entry-type', {
                acpId: acpId
            }).done((resp) => {
                if (resp == 1) {
                    window.location.href = window.location;
                } else {
                    alert(resp);
                    return false;
                }
            });
        }
    }
    const changeInternshipStatus = (acpId) => {
        if (confirm('Are you sure to change On Internship status of this section/program?')) {
            $.post('/AcademicCalendar/ac-active-programs/change-intern-status', {
                acpId: acpId
            }).done((resp) => {
                if (resp == 1) {
                    window.location.href = window.location;
                } else {
                    alert(resp);
                    return false;
                }
            });
        }
    }
    const changeGcStatus = (acpId) => {
        if (confirm('Are you sure to change graduating class status of this section/program?')) {
            $.post('/AcademicCalendar/ac-active-programs/change-gc-status', {
                acpId: acpId
            }).done((resp) => {
                if (resp == 1) {
                    window.location.href = window.location;
                } else {
                    alert(resp);
                    return false;
                }
            });
        }
    }
    activeProgId = <?= $model->id ?>;
    __gsetId = 0;
    window.showCourses = () => {
        $('#modal').find('.modal-header').html('Course Offering');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading data...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.post('/AcademicCalendar/ac-active-programs/get-course-offered', {
            activeProgId: activeProgId
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
    window.addSection = () => {
        $('#modal').find('.modal-header').html('Sections/Groups');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading data...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.post('/AcademicCalendar/ac-active-programs/add-section', {
            activeProgId: activeProgId
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
    window.groupStudents = (gsetId = __gsetId, filterMode = 0) => { //filterMode = 0 => loadAllStudents : 
        __gsetId = gsetId;
        __StdList = [];
        // console.log(gsetId,filterMode);

        $('#modal').find('.modal-header').html('Assign Students to a group/section');
        $('#modalContent').css('text-align', 'left');
        $('#modalContent').html('Loading data...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/AcademicCalendar/ac-active-programs/get-students', {
            gsetId: gsetId,
            'filterMode': filterMode
        }).done(function(data) {
            $('#modalContent').html(data);
        });



        // window.open('/w-group-setup/get-assignments2?wt_gsetUp_entry='+gsetId,'title:Student List','width:640,height: 600');
        //get-assignments?wt_gsetUp_entry=
    }
</script>
<?php

Modal::begin([
    'header' => '<h4>Penality registration payment form</h4>',
    'id' => 'modal',
    'size' => 'modal-lg',
]);
echo "<div id='modalContent'>Loading payment form...</div>";
Modal::end();

?>