<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\PaymentReason */

$this->title = Yii::t('decirs', 'Create Payment Reason');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Payment Reasons'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-reason-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
