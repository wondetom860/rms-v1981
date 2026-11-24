<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;

$this->title = 'Course Assessment';
$this->params['breadcrumbs'][] = $this->title;
?>

<div class="CourseAssessment-default-index" style="background-color: white;">
    <h3><?= $this->title ?></h3>
    <div class="row">
        <div class="col-md-5 table-responsive" style="font-size: 9pt; max-height: 450px; overflow: auto;">
            <?php // echo $this->render('_search', ['model' => $searchModel]); 
            ?>
            <?php Pjax::begin(); ?>
            <?= GridView::widget([
                'dataProvider' => $dataProvider,
                'filterModel' => $searchModel,
                'columns' => [
                    ['class' => 'yii\grid\SerialColumn'],

                    [
                        'header' => 'AcY',
                        'attribute' => 'acadYear',
                        'value' => function ($model) {
                            return $model->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc;
                        },
                        'filter' => ArrayHelper::map(\backend\models\SchoolYear::find()->all(), 'id', 'sch_year_desc'),
                    ],
                    [
                        'header' => 'Semester',
                        'attribute' => 'sem',
                        'value' => function ($model) {
                            return $model->coff2detCoff2Rel->coff2SemRel->semester_name;
                        },
                        'filter' => ArrayHelper::map(\backend\models\Semester::find()->all(), 'id', 'semester_name'),
                    ],
                    [
                        'header' => 'Department<br><span style="font-size: 7pt; font-style: italic;">(Offered to)</span>',
                        'format' => 'raw',
                        'attribute' => 'coff2det_dept_relId',
                        'value' => function ($model) {
                            return $model->coff2detCoff2Rel->coff2GroupRel->getGsDisp() . "-{$model->coff2detCoff2Rel->coff2GroupRel->gsGroupRel->group_name}";
                        },
                        'filter' => ArrayHelper::map(\backend\models\Department::find()->all(), 'id', 'dept_name'),
                    ],

                    // [
                    //     'header'=>'Class Level',
                    //     'attribute'=>'coff2det_clslvl_relId',
                    //     'value'=>'coff2detCoff2Rel.coff2GroupRel.gsClslvlRel.clslvl_num',
                    //     // 'filter' => ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_name'),
                    // ],
                    [
                        'header' => 'Courses',
                        'attribute' => 'courseTitle',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return "{$model->coff2detCourseRel->getCourseCredits()}<br><small>{$model->coff2detCourseRel->course_title}</small>";
                        },
                        // 'filter' => ArrayHelper::map(\backend\models\Course::find()->all(),'course_title','course_title'),
                    ],
                    // [
                    //     'header'=>'Course Instructor',
                    //     'attribute'=>'coff2det_inst_relId',
                    //     'value'=> function($model){

                    //         return isset($model->coff2det_inst_relId) ? $model->coff2detInstRel->instUserRel->first_name . $model->coff2detInstRel->instUserRel->middle_name[0] : '-';
                    //         // 
                    //     },
                    //     // 'filter' => ArrayHelper::map(\backend\models\Course::find()->all(),'id','course_title'), 
                    // ],
                    // [
                    //     'header'=>'Course Technical Assisstant',
                    //     'attribute'=>'coff2det_TA_relId',
                    //     'value'=> function($model){

                    //         return isset($model->coff2det_TA_relId) ? $model->coff2detTARel->instUserRel->first_name . $model->coff2detTARel->instUserRel->middle_name[0] : '-';
                    //         // return $model->coff2detCourseRel->course_title .'['.$model->coff2detCourseRel->course_code.']';
                    //     } 
                    // ],   

                    [
                        'attribute' => 'Option',
                        'format' => 'raw',
                        'value' => function ($model) {
                            return $model->coff2detCourseRel->course_type == 1 ? Html::a(
                                Yii::t('rms', 'Show'),
                                [''],
                                [
                                    'class' => 'btn btn-success btn-xs',
                                    'data-pjax' => 0,
                                    'title' => 'Asseessment mark breakdown detail',
                                    'value' => $model->id,
                                    'onclick' => 'showEvalutorsList(' . $model->id . ');return false;',
                                ]
                            ) : '';
                        }
                    ],
                ],
            ]); ?>
            <?php Pjax::end(); ?>
        </div>
        <div class="col-md-7 conatiner-fluid">
            <div class="conatiner-fluid" id="assessment_categories">
                <span>---Select course to see assessment plan detail---</span>
            </div>
            <div class="conatiner-fluid" id="faculty_assignment">
                <span>---Select course to see instructor assignment---</span>
            </div>
            <div class="conatiner-fluid" id="course_coordinator">
                <span>---Select course to see course coordinator---</span>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    coff_det_id = 0;
    window.showEvalutorsList = (id) => {
        coff_det_id = id;
        $("#assessment_categories").html("Loading course assessment plan detail");
        $.get('/CourseAssessment/cont-assess-catagory/get-categories', {
            coff2detId: id
        }).done((resp) => {
            $("#assessment_categories").html(resp);
        });

        $("#faculty_assignment").html("Loading instructor assignment data");
        $.get('/CourseAssessment/cont-assess-catagory/get-faculties', {
            coff2detId: id
        }).done((resp) => {
            $("#faculty_assignment").html(resp);
        });

        $("#course_coordinator").html("Loading course-coordinator data");
        $.get('/CourseAssessment/cont-assess-catagory/get-coordinator', {
            coff2detId: id
        }).done((resp) => {
            $("#course_coordinator").html("<h4>Course Coordinator</h4> Instructor: " + resp);
        });
    }
    // showEvalutorsList(0);
</script>