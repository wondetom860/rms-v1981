<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\models\WithdrawalReason */

$this->title = "Update Withdrawal Reason: {$model->withdrawalres_description}";
$this->params['breadcrumbs'][] = ['label' => 'Withdrawal Reasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->withdrawalres_description, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="withdrawal-reason-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
