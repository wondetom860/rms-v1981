<?php

use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcademicCalendar\models\AcActivePrograms */

$this->title = $model->academicYearRel->getAcHeader();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Calendar'), 'url' => ['/AcademicCalendar/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Programs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);

$rr = '';
?>
<div class="ac-active-programs-view container-fluid">

    <h3><?= Html::encode($this->title)." ({$model->deptRel->dept_name})" ?></h3>

    <div class="row" style="background-color: white;">
        <div class="col-md-12"  style="padding: 0px; background-color: darkgrey;">  
            <?= DetailView::widget([
                'model' => $model,
                'attributes' => [
                    
                    [
                        'attribute' => 'admission_relId',
                        'value' => function($model){
                            return $model->admissionRel->admission_name."-".$model->programRel->program_name;
                        }
                    ],[
                        'attribute' => 'dept_relId',
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->deptRel->dept_name;
                        }
                    ],[
                        'attribute' => 'class_level_relId',
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->classLevelRel->getClslvlDisp();
                        }
                    ],
                    [
                        'attribute' => 'semester_relId',
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->semesterRel?$model->semesterRel->semester_name : "-";
                        }
                    ],
                    [
                        'attribute' => 'spec_relId',
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->specRel? $model->specRel->spec_name : "-";
                        }
                    ],[
                        'attribute' => 'status',
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->status? "Active" : "Not Active";
                        }
                    ],[
                        'attribute' => 'graduate_class',
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->graduate_class? "Graduate Class" : "Non-Graduate Class";
                        }
                    ],[
                        'attribute' => 'on_internship',
                        'format' => 'raw',
                        'value' => function($model){
                            return $model->on_internship ? "On Internship" : "Non-Internship";
                        }
                    ],
                    'remark:ntext',
                    [
                        'attribute' => 'group_setups',
                        'format' => 'raw',
                        'value' => function($model){
                            return $this->render('../../../../views/w-group-setup/__report/wgroup_setups_per_acprogram',['gsModels' => $model->groupSetups]);
                        }
                    ],
                    [
                        'attribute' => 'course_offerings',
                        'format' => 'raw',
                        'value' => function($model){
                            $courses = $model->getCourseOffered();
                            if (count($courses) > 0) {
                                // $rr = 
                                return count($courses)." Course(s) Offered. 
                                    &nbsp;&nbsp;&nbsp;<button class='btn btn-xs btn-primary pull-right' onclick='showCourses();return false;' title='Show Offered Courses'>Show</button>
                                    &nbsp;&nbsp;&nbsp;<button class='btn btn-xs btn-warning pull-right' onclick='showCourseOfferingWIndow();return false;' title='Offer more course(s)'>Offer</button>
                                    <div class='col-md-12' id='course-offering' style='background-color: #FFF;'>

                                    </div>
                                ";
                            }else{
                                // check if section setup made fro the department::
                                $btn = count($model->groupSetups) > 0 ? "<button class='btn btn-xs btn-primary pull-right' onclick='showCourseOfferingWIndow();return false;'title='Offer Semester Course'>Offer</button>": "Section not set yet.";
                                return "<p class='warning'>Course Not Offered.{$btn}</p>
                                    ";
                            }
                        }
                    ],
                ],
            ]) ?>
        </div>
    </div>

</div>
<script type="text/javascript">
    activeProgId = <?= $model->id ?>;
    __offerList = [];
    window.offerCourse = () => {
        if (__offerList.length) {
            if (confirm("Offer selected course(s)")) {
                // submit course offering.
                $.post('/AcademicCalendar/ac-active-programs/offer-courses',{acpId: activeProgId,catIds: JSON.stringify(__offerList)}).done((resp) => {
                    if (resp == 1) {
                        
                    }else{
                        alert(resp);
                    }
                    showCourseOfferingWIndow();
                });
            }
        }else{
            alert('Select at least one course to offer');
        }
        
    }
    window.showCourseOfferingWIndow = () => {
        __offerList = [];
        $('#modal').find('.modal-header').html('Course Offering Window');
        $('#modalContent').css({'text-align':'left','margin-top':'0px'});
        $('#modalContent').html('Loading data...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.post('/AcademicCalendar/ac-active-programs/get-catalog-courses',{activeProgId:activeProgId}).done(function(data){
            $('#modalContent').html(data);
        });
    }
    window.showCourses = () => {
        $("#course-offering").html('Loading offering info.');
        $.post('/AcademicCalendar/ac-active-programs/get-course-offered',{activeProgId:activeProgId}).done(function(data){
            $("#course-offering").html(data);
        });
    }
    window.addSection = () => {
        $('#modal').find('.modal-header').html('Sections/Groups');
        $('#modalContent').css({'text-align':'left','margin-top':'0px'});
        $('#modalContent').html('Loading data...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.post('/AcademicCalendar/ac-active-programs/add-section',{activeProgId:activeProgId}).done(function(data){
            $('#modalContent').html(data);
        });
    }
    window.groupStudents = (gsetId) => {
        $('#modal').find('.modal-header').html('Assign Students to a group/section');
        $('#modalContent').css('text-align','left');
        $('#modalContent').html('Loading data...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/AcademicCalendar/ac-active-programs/get-students',{gsetId:gsetId,'loadAllDeptStds':1}).done(function(data){
            $('#modalContent').html(data);
        });



        // window.open('/w-group-setup/get-assignments2?wt_gsetUp_entry='+gsetId,'title:Student List','width:640,height: 600');
        //get-assignments?wt_gsetUp_entry=
    }
</script>
<?php

 Modal::begin([
    'header'=>'<h4>Penality registration payment form</h4>',
    'id'=>'modal',
    'size'=>'modal-lg',
 ]);
 echo "<div id='modalContent'>Loading payment form...</div>";
 Modal::end();

 ?>
