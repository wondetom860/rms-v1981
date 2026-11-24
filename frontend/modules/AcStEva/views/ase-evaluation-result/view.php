<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View  $this
 * @var $model frontend\modules\AcStEva\models\AseEvaluationResult 
 */

$this->title = $model->controlSettingRel->deptPlanRel->getEvaluationheader();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'AcStEva'), 'url' => ['/AcStEva/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Evaluation Results'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ase-evaluation-result-view">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']) ?>
            <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-xs',
                'data' => [
                    'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </h3>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            [
                'attribute' => 'Candidate Staff',
                'value' => function ($model) {
                    return $model->controlSettingRel->candidateStaffRel->getFullName();
                },
            ],
            [
                'attribute' => 'control_setting_relId',
                'value' => function ($model) {
                    return $model->controlSettingRel->deptPlanRel->getEvaluationheader();
                },
            ],
            [
                'attribute' => 'Evaluation Result',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->render('__report/evaluation-result', ['model' => $model]);
                }
            ],
            // [
            //     'attribute' => 'criteria_bundle_relId',
            //     'value' => function ($model) {
            //         return $model->criteriaBundleRel->code . "({$model->criteriaBundleRel->evalTypeRel->type_name})";
            //     },
            // ],
            // [
            //     'attribute' => 'completed_staffs',
            //     'format' => 'raw',
            //     'label' => 'Evaluators',
            //     'value' => function ($model) {

            //         $evs = $model->completed_staffs ? count(json_decode($model->completed_staffs, true)) : 0;
            //         return "<button class='btn btn-link btn-xs' onClick='displayCompletedEvaluators({$model->id});return false;'>{$evs}</button>";
            //     }
            // ],
            // [
            //     'attribute' => 'dept_plan_relId',
            //     'format' => 'raw',
            //     'value' => function ($model) {
            //         return $model->deptPlanRel->getScheduleInterval();
            //     }
            // ],
            // 'average_total',
            // 'average_percentage',
            // 'status',
            // [
            //     'attribute' => 'created_at',
            //     'value' => function ($model) {
            //         return date('dMY', $model->created_at);
            //     }
            // ],
            // [
            //     'attribute' => 'created_by',
            //     'value' => function ($model) {
            //         return $model->createdBy->first_name;
            //     }
            // ],
            // [
            //     'attribute' => 'updated_at',
            //     'value' => function ($model) {
            //         return $model->updated_at ? date('dMY', $model->updated_at) : null;
            //     }
            // ],
            // [
            //     'attribute' => 'updated_by',
            //     'value' => function ($model) {
            //         return $model->updatedBy ? $model->updatedBy->first_name : '';
            //     }
            // ],
            // 'data:ntext',
        ],
    ]) ?>

</div>

<script>
    window.displayCompletedEvaluators = (resultId) => {
        alert("Showing completed evaluators of " + resultId);
    }
</script>