<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\models\StudentWithdrawalHistory */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Student Withdrawal Histories', 'url' => ['approve-index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-withdrawal-history-view">

    <h1><?= Html::encode($this->title) ?></h1>

    <p>
        <?= Html::a('Update', ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
        <?= Html::a('Delete', ['delete', 'id' => $model->id], [
            'class' => 'btn btn-danger',
            'data' => [
                'confirm' => 'Are you sure you want to delete this item?',
                'method' => 'post',
            ],
        ]) ?>
    </p>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            'id',
            'swh_student_relId',
            'swh_academicyear_relId',
            'swh_semester_relId',
            'swh_withdrawalreason_relId',
            'swh_request_status',
            'swh_comment:ntext',
            // 'swh_createdBy',
            // 'swh_updatedBy',
            // 'swh_createdAt',
            // 'swh_updatedAt',
        ],
    ]) ?>

</div>
