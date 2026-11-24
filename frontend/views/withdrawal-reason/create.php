<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model frontend\models\WithdrawalReason */

$this->title = 'Create Withdrawal Reason';
$this->params['breadcrumbs'][] = ['label' => 'Withdrawal Reasons', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withdrawal-reason-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
