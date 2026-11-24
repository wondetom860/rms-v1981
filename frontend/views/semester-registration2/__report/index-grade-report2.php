<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */
/* @var $searchModel frontend\models\SemesterRegistration2Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Semester Registration');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-registration2-index">
<?php 

    Pjax::begin(); ?>    

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'semreg_std_relId',
                'value' => function($model){
                    return $model->semregStdRel->getFullName();
                }
            ],
            [
                'attribute' => 'semreg_acadYear_relId',
                'value' => function($model){
                    return $model->semregAcadYearRel->sch_year_desc;
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->groupBy('semreg_acadYear_relId')->all(),'semreg_acadYear_relId',function($model){ return $model->semregAcadYearRel->sch_year_desc; }),
            ],
            [
                'attribute' => 'semreg_semester_relId',
                'value' => function($model){
                    return $model->semregSemesterRel->semester_name;
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->groupBy('semreg_semester_relId')->all(),'semreg_semester_relId',function($model){ return $model->semregSemesterRel->semester_name; }),
            ],

            [
                'attribute' => 'std_prog_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $model->semregStdRel->stdProgRel->program_name;
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')->groupBy('student.std_prog_relId')->all(),function($model){ return $model->semregStdRel->std_prog_relId; },function($model){ return $model->semregStdRel->stdProgRel->program_name; }),
            ],
            [
                'attribute' => 'std_dept_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $model->semregStdRel->stdDeptRel->dept_name;
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')->groupBy('student.std_dept_relId')->all(),function($model){ return $model->semregStdRel->std_dept_relId; },function($model){ return $model->semregStdRel->stdDeptRel->dept_name; }),
            ],

            [
                'attribute' => 'semreg_clslvl_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $model->semregClslvlRel->getClslvlDisp();
                },
                // 'title' => function($model){
                //     return $model->semregClslvlRel->clslvlProgramrel->program_name;
                // },
                'filter' => ArrayHelper::map(\backend\models\SemesterRegistration2::find()->groupBy('semreg_clslvl_relId')->all(),'semreg_clslvl_relId',function($model){ return $model->semregClslvlRel->clslvl_name."-{$model->semregClslvlRel->clslvlProgramRel->program_name}"; }),
            ],
            [
                'attribute' => 'semreg_SGPA',
                'format' => ['decimal',2],
                'value' => function($model){
                    return $model->semreg_gradeCompleted ? $model->semreg_SGPA : NULL;
                },
            ],

            [
                'attribute' => 'semreg_CGPA',
                'format' => ['decimal',2],
                'value' => function($model){
                    return $model->semreg_gradeCompleted ? $model->semreg_CGPA : NULL;
                },
            ],

            [
                'attribute' => 'semreg_last_ssn_relId',
                'value' => function($model){
                    return $model->semesterLastSsnRel ? $model->semesterLastSsnRel->sssn_name : '-';
                },
            ],
            [
                'attribute' => 'semreg_gradeCompleted',
                'format' => 'raw',
                'value' => function($model){
                    return $model->semreg_gradeCompleted ?  "<button>Grade report</button>": 'Grade Submission not completed yet.';
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
                        'title'=>'Show Course Grade',
                        'value'=>$model->id,
                        'onclick'=>'showEnrollments('.$model->id.');return false;',
                    ]);
                }
            ],

            // 'semreg_gradeCompleted',
            // 'semreg_approved',
            // 'semreg_approved_by',
            // 'semreg_approved_at',
            // 'semreg_created_by',
            // 'semreg_updated_by',
            // 'semreg_created_at',
            // 'semreg_updated_at',

            // [
            //     // 'class' => 'yii\grid\ActionColumn'
            // ],
        ],
    ]); ?>

    <script type="text/javascript">
        __semregId = null;
        window.showEnrollments = (semregId) => {
            __semregId = semregId;
            $('#modal').find('.modal-header').html('Course Enrollments');
            $('#modalContent').css('text-align','left');
            $('#modalContent').html('Loading data...');
            $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
            $('#modal').modal('show');
            $.post('/semester-registration2/get-srd2-detail',{semregId:semregId}).done(function(data){
                $('#modalContent').html(data);
            });
        }
    </script>
<?php Pjax::end(); 
?>
</div>

<?php
    // $y = \Yii::$app->user->isGuest;
    Modal::begin([
        'header'=>'<h4></h4>',
        'id'=>'modal',
        'size'=>'modal-sm',
    ]);
    echo "<div id='modalContent'>Loading report...</div>";
    Modal::end();
?>
