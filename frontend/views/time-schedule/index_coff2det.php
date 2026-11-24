<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\CourseOffering2DetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Course Offerings - Class Scheduling Entry');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-offering2-detail-index">
    <div class="container-fluid">
        <h3><?= Html::encode($this->title) ?></h3>
        <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

        <div class="row" style="background-color: #FFF;">
            <div class="col-md-9 container-fluid table-responsive" style="max-height: 410px; overflow-x: auto;
             border-bottom: 1px solid black;">
                <?php Pjax::begin(); ?>    
                    <?= GridView::widget([
                        'dataProvider' => $dataProvider,
                        'filterModel' => $searchModel,
                        'columns' => [
                            ['class' => 'yii\grid\SerialColumn'],

                            [
                                'attribute' => 'acadYear',
                                'value' => function($model){
                                    return $model->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc;
                                },
                                'filter' => ArrayHelper::map(\backend\models\CourseOffering2::find()->groupBy('coff2_acadYear_relId')->all(),'coff2_acadYear_relId', function($model){ return $model->coff2AcadYearRel->sch_year_desc; } )
                            ],[
                                'attribute' => 'sem',
                                'value' => function($model){
                                    return $model->coff2detCoff2Rel->coff2SemRel->semester_name;
                                },
                                'filter' => ArrayHelper::map(\backend\models\CourseOffering2::find()->groupBy('coff2_sem_relId')->all(),'coff2_sem_relId', function($model){ return $model->coff2SemRel->semester_name; } )
                            ],[
                                'attribute' => 'coff2det_course_relId',
                                'format' => 'raw',
                                'value' => function($model){
                                    return $model->coff2detCourseRel->course_title."[{$model->coff2detCourseRel->course_code}&nbsp;&nbsp;[{$model->coff2detCourseRel->course_crdthr}-{$model->coff2detCourseRel->course_lecture_hour}-{$model->coff2detCourseRel->course_lab_hour}-{$model->coff2detCourseRel->course_tutorial_hour}]]";
                                },
                                'filter' => ArrayHelper::map(\backend\models\CourseOffering2Detail::find()->groupBy('coff2det_course_relId')->all(),'coff2det_course_relId', function($model){ return $model->coff2detCourseRel->course_title; } )
                            ],[
                                'attribute' => 'coff2det_inst_relId',
                                'value' => function($model){
                                    return $model->coff2det_inst_relId && $model->coff2detInstRel ? $model->coff2detInstRel->getFullName() : ' - ';
                                },
                                'filter' => ArrayHelper::map(\backend\models\CourseOffering2Detail::find()->groupBy('coff2det_inst_relId')->all(),'coff2det_inst_relId', function($model){ return $model->coff2det_inst_relId && $model->coff2detInstRel ? $model->coff2detInstRel->getFullName() : null; } )
                            ],

                            [
                                'attribute' => 'group',
                                'format' => 'raw',
                                'value' => function($model){
                                    return $model->coff2detCoff2Rel->coff2GroupRel->gsGroupRel->group_name."[<span style='font-size: 5pt'>{$model->coff2detCoff2Rel->coff2GroupRel->getGsDisp()}</span>]";
                                },
                                'filter' => ArrayHelper::map(\backend\models\CourseOffering2::find()->groupBy('coff2_group_relId')->all(),'coff2_group_relId', function($model){ return $model->coff2GroupRel->gsGroupRel->group_name; } )
                            ],

                            [
                                'attribute' => 'scheduled',
                                'value' => function($model){
                                    return $model->getScheduledCount();
                                }
                            ],

                            [
                                'attribute'=>'Option',
                                'format'=>'raw',
                                'visible' => function($model){
                                    return !is_null($model->coff2det_inst_relId);
                                },
                                'value'=>function($model){
                                    return Html::a(Yii::t('rms', 'Show'), [''], 
                                        [
                                        'class' => 'btn btn-success btn-xs btn___',
                                        'data-pjax'=>0,
                                        'id'=>'__cs'.$model->id,
                                        'title'=>'Class Schedule',
                                        'value'=>$model->id,
                                        'onclick'=>'showEvalutorsList('.$model->id.');return false;',
                                    ]);
                                }
                            ],
                            [
                                'attribute'=>'Option',
                                'format'=>'raw',
                                'value'=>function($model){
                                    return Html::a(Yii::t('rms', 'Report 1'), [''], 
                                        [
                                        'class' => 'btn btn-primary btn-xs btn_reprt_1___',
                                        'data-pjax'=>0,
                                        'id'=>'__cs_reprt_1'.$model->id,
                                        'title'=>'Generate schedule report per group',
                                        'value'=>$model->id,
                                        'onclick'=>'generateReport(1,'.$model->id.');return false;',
                                    ]);
                                }
                            ],
                            [
                                'attribute'=>'Option',
                                'format'=>'raw',
                                'value'=>function($model){
                                    return Html::a(Yii::t('rms', 'Report 2'), [''], 
                                        [
                                        'class' => 'btn btn-warning btn-xs btn_reprt_2___',
                                        'data-pjax'=>0,
                                        'id'=>'__cs_reprt_2'.$model->id,
                                        'title'=>'Generate schedule report per instructor',
                                        'value'=>$model->id,
                                        'onclick'=>'generateReport(2,'.$model->id.');return false;',
                                    ]);
                                }
                            ],
                        ],
                    ]); ?>
                <?php Pjax::end(); ?>
            </div>
            <div class="col-md-3">
                <br>
                <div class="hidden" id="schedule_main_entry">
                    <h5>Class type and Room #</h5>
                    <div class="row" style="background-color: #EDF; padding: 10px;">
                        <div class="col-md-12">
                            <label for="class_type">Choose Class Type</label>
                            <select class="form-control" id="class_type">
                                <option value="Lec">Lecture</option>
                                <option value="Tut">Tutorial</option>
                                <option value="Lab">Laboratory</option>
                            </select>
                        </div>
                        <div class="col-md-12">
                            <label for="class_type">Room #</label>
                            <?= $this->render('../../modules/ClassSchedule/views/cs-available-class-room/__report/get-list-for-selection') ?>
                        </div>
                        
                        <div class="col-md-12"><br>
                            <button class="btn btn-sm btn-warning pull-right" onclick="loadForm(); return false;" style="margin-top: 7pt;">Load Schedule</button>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
        <br>
        <div class="row" style="background-color: #FFF;">
            <div class="col-md-12">
                <div class="detail-container"></div>
            </div>
        </div>
    </div>
</div>

<?php
    // $y = \Yii::$app->user->isGuest;
    Modal::begin([
        'header'=>'<h4></h4>',
        'id'=>'modal',
        'size'=>'modal-sm',
    ]);
    echo "<div id='modalContent'>Loading form...</div>";
    Modal::end();
?>

<script type="text/javascript">

    window.generateReport = (type,coffdetId)=>{
        string = JSON.stringify({'type':type,'coffdet':coffdetId});
        myWindow = window.open(
          '/ClassSchedule/cs-class-schedule-detail/get-schedule-report?queryString='+string, 
          'title=Class time schedule report', 
          'height=700,width=900');

    }
    __coff2detId = null;
    window.loadForm = ()=>{
        $('.detail-container').html('Loading schedule...');
        type = $("#class_type :selected").val();
        room = $("#avaliabe_room :selected").val();
        // console.log(type,room);
        $.post('/ClassSchedule/cs-class-schedule-detail/get-schedule',{id:__coff2detId,class_type:type,room:room}).done((data)=>{
            $('.detail-container').html(data);
        });
    }
    window.showEvalutorsList = function(id){
        $('.detail-container').html('select...');
        $('.btn___').removeClass('btn-info');
        $("#__cs"+id).addClass('btn-info');
        $("#schedule_main_entry").removeClass('hidden');
        __coff2detId = id;
    }
</script>
