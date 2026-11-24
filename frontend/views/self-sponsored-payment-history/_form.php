<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SelfSponsoredPaymentHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="self-sponsored-payment-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ssh_std_relId')->textInput() ?>

    <?= $form->field($model, 'ssph_acadYear_relId')->textInput() ?>

    <?= $form->field($model, 'ssh_sem_relId')->textInput() ?>

    <?= $form->field($model, 'ssh_approved')->textInput() ?>

    <?= $form->field($model, 'ssh_approved_at')->textInput() ?>

    <?= $form->field($model, 'ssh_approved_by')->textInput() ?>

    <?= $form->field($model, 'ssh_txn_num')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ssh_RCT_scan_path')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
