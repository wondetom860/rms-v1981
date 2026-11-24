<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\GC\models\GcEntry */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gc-entry-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'header_title')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'senate_approve_date_str')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
