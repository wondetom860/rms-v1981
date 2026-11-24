<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\Program $model*/

$this->title = $model->program_name . ": " . $model->program_desc;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Programs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="program-view">

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
                'attribute' => 'program_adm_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->programAdmRel ? Html::a($model->programAdmRel->admission_name, ['admission/view', 'id' => $model->programAdmRel->id]) : '';
                }
            ],
            'program_name',
            [
                'attribute' => 'program_status',
                'value' => function ($model) {
                    return $model->program_status ? Yii::t('rms', 'Active') : Yii::t('rms', 'Inactive');
                }
            ],
            'program_desc:ntext',
            [
                'attribute' => 'program_created_by',
                'value' => function ($model) {
                    return $model->programCreatedBy ? $model->programCreatedBy->username : '';
                }
            ],
            [
                'attribute' => 'program_updated_by',
                'value' => function ($model) {
                    return $model->programUpdatedBy ? $model->programUpdatedBy->username : '';
                }
            ],
            [
                'attribute' => 'program_updated_at',
                'format' => ['date', 'php:dMY'],
            ],
            [
                'attribute' => 'program_created_at',
                'format' => ['date', 'php:dMY'],
            ],
        ],
    ]) ?>

</div>