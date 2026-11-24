<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\models\ReadmissionHistory */

$this->title = 'Approve Readmission: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Readmission Histories', 'url' => ['approve-index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
//$this->params['breadcrumbs'][] = 'Update';
?>
<div class="readmission-history-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formapprove', [
        'model' => $model,
    ]) ?>

</div>
