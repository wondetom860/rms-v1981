<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\models\Attendancedetail */

$this->title = $model->id;
$this->params['breadcrumbs'][] = ['label' => 'Attendancedetails', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attendancedetail-view">

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
            'ad_attendance_relId',
            'ad_student_relId',
            'ad_status',
            'ad_remark:ntext',
        ],
    ]) ?>

</div>
