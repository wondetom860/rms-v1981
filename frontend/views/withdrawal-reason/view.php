<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\models\WithdrawalReason */

$this->title = $model->withdrawalres_description;
$this->params['breadcrumbs'][] = ['label' => 'Withdrawal Reasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withdrawal-reason-view">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="pull-right">
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger btn-xs',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            //'id',
            'withdrawalres_description',
            'remove_course_enrolls',
            'readmittable',
            // 'withdrawalres_updatedAt',
            // 'withdrawalres_createdBy_relId',
            // 'withdrawalres_createdAt',
            // 'withdrawalres_updatedby_relId',
        ],
    ]) ?>

</div>
