<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\PaymentRedemption */

$this->title = Yii::t('rms', 'Create Payment Exemption');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Payment Exemptions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="payment-redemption-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
