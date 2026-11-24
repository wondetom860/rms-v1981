<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\bootstrap\Modal;

/** @var yii\web\View  $this*/
/* @var $searchModel backend\models\SemesterEvaluation2Search */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Semester Evaluation Progress');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-evaluation2-index table-responsive">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <!-- <p>
        <?= Html::a(Yii::t('rms', 'Create Semester Evaluation2'), ['create'], ['class' => 'btn btn-success']) ?>
    </p> -->
    <?php Pjax::begin(['id' => 'semester_evaluation_2']);
    $action = ''; ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'rowOptions' => function ($model) {
            $evalType = $model->semevEvalTypeRel;
            if ($evalType->semev_type_level == 4) { //Eval type is EVALUATE_INST
                return ['class' => 'success'];
            } else if ($evalType->semev_type_level == 5) { //Eval Type is EVALUATE_TAs.
                return ['class' => 'warning', 'title' => 'Evaluation progress',];
            } else {
                return ['class' => 'danger'];
            }
        },
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'semev_acadYear_relId',
                'value' => 'semevAcadYearRel.sch_year_desc',
                'filter' => ArrayHelper::map(\backend\models\SemesterEvaluation2::find()->groupBy('semev_acadYear_relId')->all(), 'semev_acadYear_relId', function ($model) {
                    return $model->semevAcadYearRel->sch_year_desc;
                })
            ],
            [
                'attribute' => 'semev_sem_relId',
                'value' => 'semevSemRel.semester_name',
                'filter' => ArrayHelper::map(\backend\models\SemesterEvaluation2::find()->groupBy('semev_sem_relId')->all(), 'semev_sem_relId', function ($model) {
                    return $model->semevSemRel->semester_name;
                })
            ],
            [
                'attribute' => 'semev_evaluee_relId',
                'value' => function ($model) {
                    return $model->semevEvalueeRel->getFullName();
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterEvaluation2::find()->groupBy('semev_evaluee_relId')->all(), 'semev_evaluee_relId', function ($model) {
                    return $model->semevEvalueeRel->getFullName();
                })

            ],
            [
                'attribute' => 'semev_group_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->semev_group_relId ? $model->semevGroupRel->getGsDisp() : 'N/A';
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterEvaluation2::find()->groupBy('semev_group_relId')->all(), 'semev_group_relId', function ($model) {
                    return $model->semev_group_relId ? $model->semevGroupRel->gsGroupRel->group_name : 'N/A';
                })
            ],
            [
                'attribute' => 'semev_course_relId',
                'value' => function ($model) {
                    return $model->semev_course_relId ? $model->semevCourseRel->course_title : 'N/A';
                },
                'filter' => ArrayHelper::map(\backend\models\SemesterEvaluation2::find()->groupBy('semev_course_relId')->all(), 'semev_course_relId', function ($model) {
                    return $model->semev_course_relId ? $model->semevCourseRel->course_title : 'N/A';
                })
            ],
            [
                'attribute' => 'semev_evalType_relId',
                'value' => 'semevEvalTypeRel.semev_type_desc',
                'filter' => ArrayHelper::map(\backend\models\SemesterEvaluation2::find()->groupBy('semev_evalType_relId')->all(), 'semev_evalType_relId', function ($model) {
                    return $model->semev_evalType_relId ? $model->semevEvalTypeRel->semev_type_desc : 'N/A';
                })
            ],
            [
                'attribute' => 'semev_averagePoint',
                'value' => function ($model) {
                    $result = \backend\models\SemesterEvaluationReportController::getReport($model->id);
                    if (count($result[1]) == 0) {
                        # code...
                        return '-';
                    }
                    return count($result[0]) . '/' . count($result[1]);
                }
            ],
            [
                'attribute' => 'action',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a(
                        Yii::t('rms', 'Show'),
                        [''],
                        [
                            'class' => 'btn btn-success btn-xs',
                            'data-pjax' => 0,
                            'title' => 'Show Evaluators',
                            'value' => $model->id,
                            'onclick' => 'showEvalutorsList(' . $model->id . ');return false;',
                        ]
                    );
                }
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'template' => '{view} {update} {delete}',
                'controller' => 'semester-evaluation',
                'visibleButtons' => [
                    'view' => function ($model, $key, $index) {
                        return false;
                    },
                    'update' => function ($model, $key, $index) {
                        return false;
                        // return count($model->semesterEvaluation2Details) == 1 && Yii::$app->user->can('DepartmentHead');
                    },
                    'delete' => function ($model, $key, $index) {
                        return count($model->semesterEvaluation2Details) == 1 && (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin'));
                    },
                ]
            ],
            // [
            //     'attribute' => 'Option',
            //     'format' => 'raw',
            //     'value' => function ($model) {
            //         return Html::a('Delete', ['semester-evaluation-2/delete', 'id' => $model->id]);
            //     }
            // ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>
<?php
Modal::begin([
    'header' => '<h4> Semester Evaluation progress report </h4>',
    'id' => 'modal',
    'size' => 'modal-lg',
]);
echo "<div id='modalContent'>Loading report</div>";
Modal::end();
?>
<?php
$this->registerJs(
    "
    $('.showLists').click(function(){
    id = $(this).attr('value');
    //alert(id);
    showEvalutorsList(id);
    return false;
    });

    window.showEvalutorsList = function(id){
        $('#modalContent').css('text-align','left');
        $('#modalContent').html('Loading data...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.post('/semester-evaluation-progress/get-list',{id:id}).done(function(data){
                $('#modalContent').html(data);
            });
    }
    ",
    $this::POS_END
);

?>