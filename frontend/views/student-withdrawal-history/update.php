<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\models\StudentWithdrawalHistory */

$this->title = 'Update Student Withdrawal History: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Student Withdrawal Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="student-withdrawal-history-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
