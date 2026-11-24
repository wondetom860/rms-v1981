<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ExamSchedule\models\ExInvigilators */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Main'), 'url' => ['/ExamSchedule/default/index']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Invigilators'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="ex-invigilators-view">

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
            'exam_room_slot_relId',
            'section_relId',
            'offering_detail_relId',
            'staff_relId',
            'created_at',
            'created_by',
            'updated_at',
            'updated_by',
            'data:ntext',
        ],
    ]) ?>

</div>
