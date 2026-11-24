<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SelfSponsoredPaymentHistorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="self-sponsored-payment-history-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ssh_std_relId') ?>

    <?= $form->field($model, 'ssph_acadYear_relId') ?>

    <?= $form->field($model, 'ssh_sem_relId') ?>

    <?= $form->field($model, 'ssh_approved') ?>

    <?php // echo $form->field($model, 'ssh_approved_at') ?>

    <?php // echo $form->field($model, 'ssh_approved_by') ?>

    <?php // echo $form->field($model, 'ssh_txn_num') ?>

    <?php // echo $form->field($model, 'ssh_RCT_scan_path') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
