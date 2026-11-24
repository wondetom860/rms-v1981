<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\models\Attendancedetail */

$this->title = 'Update Attendancedetail: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Attendancedetails', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="attendancedetail-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
