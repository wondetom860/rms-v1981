<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/** @var backend\models\Specialization $model */

$this->title = $model->spec_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Specializations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="specialization-view">

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
            'spec_code',
            [
                'attribute' => 'spec_prog_relId',
                'value' => function ($model) {
                    return $model->specAdmRel->admission_name . ' ' . $model->specProgRel->program_name;
                }
            ],
            [
                'attribute' => 'spec_dep_relId',
                'value' => function ($model) {
                    return $model->specDepRel->dept_name;
                }
            ],
            'spec_name',
            'spec_desc:ntext',
            [
                'attribute' => 'spec_status',
                'value' => function ($model) {
                    return $model->spec_status ? 'Active' : 'In-Active';
                }
            ],
        ],
    ]) ?>

</div>