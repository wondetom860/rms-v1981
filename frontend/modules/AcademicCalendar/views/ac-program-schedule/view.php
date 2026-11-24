<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View  $this
 * @var $model frontend\modules\AcademicCalendar\models\AcProgramSchedule 
 */

$this->title = $model->actionRel->aca_name . " [{$model->activeProgramRel->academicYearRel->getAcHeader()}]";
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Calendar'), 'url' => ['/AcademicCalendar/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Program Schedules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ac-program-schedule-view">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-xs btn-primary']) ?>
            <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-xs btn-danger',
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
                'attribute' => 'active_program_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->render('../ac-active-programs/__report/index', ['records' => [$model->activeProgramRel], 'options' => false]);
                }
            ],
            [
                'attribute' => 'action_relId',
                'value' => $model->actionRel->aca_name,
            ],
            [
                'attribute' => 'start_date',
                'value' => date("d M, Y", $model->start_date)
            ],
            [
                'attribute' => 'end_date',
                'value' => date("d M, Y h:i A", $model->end_date)
            ],
            [
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->getStatus();
                }
            ],
            // 'last_extend',
            // 'last_extend_status',
            'remark:ntext',
            [
                'attribute' => 'created_at',
                'value' => date("d M, Y h:i A", $model->created_at)
            ],
            [
                'attribute' => 'created_by',
                'value' => $model->createdBy->getFullName_av(),
            ],
        ],
    ]) ?>

</div>