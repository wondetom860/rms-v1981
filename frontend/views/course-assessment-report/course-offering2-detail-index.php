<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\CourseOffering2DetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Course Assessment Report');
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-9">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 style="text-decoration: underline;"><?= Html::encode($this->title) ?></h4>
                </div>
                <div class="panel-body table-responsive" style="font-size: 9pt;">
                    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
                    <?php Pjax::begin(); ?>    <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],

                                [
                                    'header'=>'Academic Year',
                                    'attribute'=>'acadYear',
                                    'value'=> function($model){
                                        return $model->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc;
                                    } ,
                                    'filter' => ArrayHelper::map(\backend\models\SchoolYear::find()->all(),'id','sch_year_desc'),
                                ],
                                [
                                    'header'=>'Semester',
                                    'attribute'=>'sem',
                                    'value'=>function($model){
                                        return $model->coff2detCoff2Rel->coff2SemRel->semester_name;
                                    },
                                    'filter' => ArrayHelper::map(\backend\models\Semester::find()->all(),'id','semester_name'),
                                ],

                                [
                                    'header'=>'Courses',
                                    'attribute'=>'courseTitle',
                                    'value'=> function($model){
                                        return $model->coff2detCourseRel->course_title .'['.$model->coff2detCourseRel->course_code.','.$model->coff2detCourseRel->course_crdthr.']';
                                    } ,
                                    // 'filter' => ArrayHelper::map(\backend\models\Course::find()->all(),'course_title','course_title'),
                                ],
                                [
                                    'header'=>'Class Level',
                                    'format' => 'raw',
                                    'attribute'=>'coff2det_clslvl_relId',
                                    'value'=>function($model){
                                        return $model->coff2detCoff2Rel->coff2GroupRel->gsClslvlRel->getClslvlDisp();
                                    },
                                    // 'filter' => ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_name'),
                                ],
                                [
                                    'header'=>'Group',
                                    'attribute'=>'coff2det_group_relId',
                                    'format' => 'raw',
                                    'value'=> function($model){
                                        return $model->coff2detCoff2Rel->coff2GroupRel->gsGroupRel->group_name."({$model->coff2detCoff2Rel->coff2GroupRel->gsDeptRel->dept_code})";
                                    },
                                    // 'filter' => ArrayHelper::map(\backend\models\Course::find()->all(),'id','course_title'), 
                                ], 

                                [
                                    'attribute'=>'Option',
                                    'format'=>'raw',
                                    'value'=>function($model){
                                        return Html::a(Yii::t('rms', 'Print'), ['#'], 
                                            [
                                                'class' => 'btn btn-success btn-xs',
                                                'data-pjax'=>0,
                                                'title'=>'Print Mark Sheet',
                                                'value'=>$model->id,
                                                'onclick'=>"markSheet({$model->id});return false;",
                                        ]);
                                    }
                                ],
                                [
                                    'attribute'=>'Print',
                                    'format'=>'raw',
                                    'value'=>function($model){
                                        return Html::a(Yii::t('rms', 'Print'), ['#'], 
                                            [
                                                'class' => 'btn btn-warning btn-xs',
                                                'data-pjax'=>0,
                                                'title'=>'Print Grade Report',
                                                // 'value'=>$model->id,
                                                'onclick'=>"gradeReport({$model->id});return false;",
                                        ]);
                                    }
                                ],          
                            ],
                        ]); ?>
                    <?php Pjax::end(); ?>  
                </div>
            </div>
        </div>
        <!-- <div class="col-md-6">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h5>Course Assessment Detail</h5>
                </div>
                <div class="panel-body detail-container">
                    
                </div>
            </div>
        </div> -->
    </div>
</div>

<!-- continous-assessment2  => ContinousAssessment2Controller -->

<!-- get-mark-sheet-detail => actionGetMarkSheetDetail -->

<script type="text/javascript">
    window.markSheet = function(coff2detId){
        myWindow = window.open(
        '/continuous-assessment2/get-mark-sheet-detail?params='+JSON.stringify([coff2detId,23,434]), 
          'Print Students Mark Sheet Report', 
          'height=900,width=1100');
        return false;
    }

    window.gradeReport = function(coff2detId){
        myWindow = window.open(
        '/continuous-assessment2/get-grade-report-detail?params='+JSON.stringify([coff2detId,24,545]), 
          'Print Students Grade Report', 
          'height=1000,width=700');
        return false;
    }

    window.showEvalutorsList = function(id){
        $('.detail-container').html('Loading....');
        $.post('/course-offering2-detail/get-detail',{id:id}).done((data)=>{
            $('.detail-container').html(data);
        });
    }
</script>