<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\GraduateStudentRegistrationEntryHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="graduate-student-registration-entry-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'gphr_acadYear')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'gphr_semester')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
