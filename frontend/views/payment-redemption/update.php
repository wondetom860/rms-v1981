<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\PaymentRedemption */

$this->title = Yii::t('rms', 'Update Payment Exemption: {name}', [
    'name' => $model->stdRel->getFullName() . ' >> '.$model->reason,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Payment Exemptions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="payment-redemption-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
