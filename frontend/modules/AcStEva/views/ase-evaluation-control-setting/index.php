<?php

use backend\models\Department;
use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\AcStEva\models\AseEvaluationControlSettingSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Evaluation Control Settings');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'AcStEva'), 'url' => ['/AcStEva/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ase-evaluation-control-setting-index table-responsive">

    <h3><?= Html::encode($this->title) ?></h3>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'academic_year',
                'label' => 'Academic Year',
                'value' => function ($model) {
                    return $model->deptPlanRel->headerRel->academicYearRel->getAcHeader();
                },
                'filter' => \yii\helpers\ArrayHelper::map(
                    \frontend\modules\AcStEva\models\AseSemesterEvaluation::find()->all(),
                    'id',
                    function($model){
                        return $model->academicYearRel->getAcHeader();
                    }
                )
            ],

            [
                'attribute' => 'dept_plan_relId',
                'value' => function ($model) {
                    return $model->deptPlanRel->deptRel->dept_code;
                },
                'filter' => \yii\helpers\ArrayHelper::map(
                    Department::find()->all(),
                    'id',
                    function($model){
                        return $model->dept_code;
                    }
                )
            ],
            [
                'attribute' => 'candidate_staff_relId',
                'value' => function ($model) {
                    return $model->candidateStaffRel->getFullName();
                }
            ],
            [
                'attribute' => 'course',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->deptPlanRel->criteriaBundleRel->evaluate_per_course && $model->course_relId ? "Course : {$model->courseRel->getCourseCredits()}" : '';
                }
            ],
            'evaluated_as',
            [
                'attribute' => 'evaluators',
                'format' => 'raw',
                'value' => function ($model) {
                    $done = $model->getDoneCount();
                    $count = count(json_decode($model->evaluators));
                    return "<button class='btn btn-link btn-xs' onClick='displayEvaluators({$model->id});return false;'>{$count}/{$done}</button>";
                }
            ],
            [
                'attribute' => 'total_percentage',
                'format' => 'raw',
                'value' => function ($model) {
                    $model->calculateTotalAverage();
                    $count = count(json_decode($model->evaluators));
                    $done = $model->getDoneCount();
                    if ($model->total_percentage > 0) {
                        $total = sprintf('%0.2f', $model->total_percentage);
                    } else {
                        $total = 0;
                    }
                    $color = $count > 0 ? (($done / $count) >= .51 ? 'green' : 'red') : 'black';
                    return "<span style='color:{$color}; font-weight:bold;'>{$total}</span>";
                }
            ],
            // 'total_percentage',
            'finalized',
            [
                'attribute' => 'created_by',
                'value' => function ($model) {
                    return $model->createdBy->username;
                }
            ],
            // [
            //     'attribute' => 'created_at',
            //     'value' => function($model){
            //         return date("d/m/Y",$model->created_at);
            //     }
            // ],
            //'created_at',
            //'updated_at',
            //'updated_by',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<script type="text/javascript">
    const displayEvaluators = (controlId) => {
        // __candidate_staff_id = '';
        // __evaluators = [];
        // __bundle_id = bundleId;
        $('#modal').find('.modal-header').html('Evaluation Criteria Deatils');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/AcStEva/ase-evaluation-control-setting/show-evaluators', {
            id: controlId
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
</script>