<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\SentPublishdGrades */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sent-publishd-grades-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'recipient_email')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'relation')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
