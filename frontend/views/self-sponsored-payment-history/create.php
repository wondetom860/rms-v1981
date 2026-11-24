<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\SelfSponsoredPaymentHistory */

$this->title = Yii::t('decirs', 'Create Self Sponsored Payment History');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Self Sponsored Payment Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="self-sponsored-payment-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
