<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\CourseOffering2DetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'This Semster Course Assessment Mark Distribution Report');
//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h4 style="text-decoration: underline;"><?= Html::encode($this->title) ?></h4>
                </div>
                <div class="panel-body table-responsive" style="font-size: 9pt; height: 530px; overflow-y: auto;">
                    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>
                    <?php Pjax::begin(); ?>    <?= GridView::widget([
                            'dataProvider' => $dataProvider,
                            'filterModel' => $searchModel,
                            'columns' => [
                                ['class' => 'yii\grid\SerialColumn'],

                                // [
                                //     'header'=>'Acad Year-Sem',
                                //     'attribute'=>'coff2det_coff2_relId',
                                //     'value'=> function($model){
                                //         return $model->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc .'-'. $model->coff2detCoff2Rel->coff2SemRel->semester_name  ;
                                //     } ,
                                // ],
                                [
                                    'header'=>'Department',
                                    'attribute'=>'coff2det_dept_relId',
                                    'value'=>'coff2detCocatRel.cocatDeptRel.dept_name',
                                    'filter' => ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_name'),
                                ],

                                // [
                                //     'header'=>'Class Level',
                                //     'attribute'=>'coff2det_clslvl_relId',
                                //     'value'=>'coff2detCoff2Rel.coff2GroupRel.gsClslvlRel.clslvl_num',
                                //     // 'filter' => ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_name'),
                                // ],
                                [
                                    'header'=>'Courses',
                                    'attribute'=>'courseTitle',
                                    'value'=> function($model){
                                        return $model->coff2detCourseRel->course_title .'['.$model->coff2detCourseRel->course_code.','.$model->coff2detCourseRel->course_crdthr.']';
                                    } ,
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
                                    'attribute'=>'Option',
                                    'format'=>'raw',
                                    'value'=>function($model){
                                        return Html::a(Yii::t('rms', 'Show'), [''], 
                                            [
                                            'class' => 'btn btn-success btn-xs',
                                            'data-pjax'=>0,
                                            'title'=>'Asseessment mark breakdown detail',
                                            'value'=>$model->id,
                                            'onclick'=>'showEvalutorsList('.$model->id.');return false;',
                                        ]);
                                    }
                                ],         
                            ],
                        ]); ?>
                    <?php Pjax::end(); ?>  
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-warning">
                <div class="panel-heading">
                    <h5>Course, Offering, and Assessment Information</h5>
                </div>
                <div class="panel-body detail-container">
                    
                </div>
            </div>
        </div>
    </div>
</div>


<script type="text/javascript">
    window.showEvalutorsList = function(id){
        $('.detail-container').html('Loading....');
        $.post('/course-offering2-detail/get-detail',{id:id}).done((data)=>{
            $('.detail-container').html(data);
        });
    }
</script>