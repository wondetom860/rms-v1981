<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\CourseOffering2DetailSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$hod = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
$dataProvider->query->groupBy('coff2det_course_relId');


$this->title = Yii::t('rms', 'Course Offering and Enrollment Detail');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-offering2-detail-index table-responsive">

    <h4><?= Html::encode($this->title) ?></h4>
    <?="<button class='btn btn-warning btn-xs pull-right' onclick='printContent();return false;'>Print</button>" ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'acadYear',
                'value' => function($model){
                    return $model->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc;
                },
                'filter' => ArrayHelper::map(\backend\models\CourseOffering2Detail::find()->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')->groupBy('coff2_acadYear_relId')->all(), function($model){ return $model->coff2detCoff2Rel->coff2_acadYear_relId; } ,function($model){ return $model->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc; })
            ],[
                'attribute' => 'sem',
                'value' => function($model){
                    return $model->coff2detCoff2Rel->coff2SemRel->semester_name;
                },
                'filter' => ArrayHelper::map(\backend\models\CourseOffering2Detail::find()->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')->groupBy('coff2_sem_relId')->all(), function($model){ return $model->coff2detCoff2Rel->coff2_sem_relId; } ,function($model){ return $model->coff2detCoff2Rel->coff2SemRel->semester_name; })
            ],
            [
                'attribute' => 'courseTitle',
                'format' => 'raw',
                'value' => function($model){
                    return "<a onclick='showCourseOfferingHistory({$model->id}); return false;'>[{$model->coff2detCourseRel->course_code}] ".$model->coff2detCourseRel->course_title."</a>";
                }
            ],
            
            [
                'attribute'=>'offering',
                'header' => 'Offering',
                'format'=>'raw',
                'value' => function($model){
                    return $this->render('__report/offering-report',['model' => $model]);
                },
            ],

            [
                'attribute'=>'Option',
                'format'=>'raw',
                'value'=>function($model){
                    return Html::a(Yii::t('rms', 'Show'), [''], 
                        [
                        'class' => 'btn btn-success btn-xs',
                        'data-pjax'=>0,
                        'title'=>'Show enrollments',
                        'value'=>$model->id,
                        'onclick'=>'showEnrollments('.$model->id.');return false;',
                    ]);
                }
            ],         
            
            // 'coff2det_created_by',
            // 'coff2det_created_at',
            // 'coff2det_updated_at',
            // 'coff2det_updated_by',
        ],
    ]); ?>
    <script type="text/javascript">
        window.printContent = () => {
            myWindow = window.open(
              '/course-offering2-detail/get-offering-history-print', 
              'title=Course Offering report', 
              'height=600,width=1000');
        }

        window.showEvaluation = (offeringDetailModel,ta)=>{
            // window.showEvaluation = () => {
            myWindow = window.open(
              '/generate-semester-evaluation-report/get-report?coffDetId='+offeringDetailModel+'&ta='+ta+'&__loyout_print=1', 
              'title=Generate Evaluation Report', 
              'height=900,width=800');
        }
        window.showCourseOfferingHistory = (coff2detId) => {
            $('#modal').find('.modal-header').html('Course Offering History');
            $('#modalContent').css('text-align','left');
            $('#modalContent').html('Loading data...');
            $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
            $('#modal').modal('show');
            $.get('/course-offering2-detail/get-offering-history',{coff2detId:coff2detId}).done(function(data){
                $('#modalContent').html(data);
            });
        }
        window.showPointHistory = (stdId) =>{
            $("#point_history_container").focus();
            $("#point_history_container").html('Loading semester registration & point history....');

            $('.btn__stds').removeClass('btn-info');
            $('#btn__std'+stdId).addClass('btn-info');

            $.post('/student/get-registration-history',{stdId:stdId}).done((resp) =>{
                $("#point_history_container").html(resp);
            });
        }

        __coff2detId = null;
        window.showEnrollments = (coff2detId) => {
            __coff2detId = coff2detId;
            $('#modal').find('.modal-header').html('Course Enrollments');
            $('#modalContent').css('text-align','left');
            $('#modalContent').html('Loading data...');
            $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
            $('#modal').modal('show');
            $.get('/course-offering2-detail/get-enrollments',{coff2detId:coff2detId}).done(function(data){
                $('#modalContent').html(data);
            });
            // return false;

            // window.open('/w-group-setup/get-assignments?wt_gsetUp_entry='+gsetId,"height=600,width=800","Student List");
        }
    </script>
<?php Pjax::end(); ?></div>
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
