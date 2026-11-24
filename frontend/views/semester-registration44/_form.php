<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-registration-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sr_student_relId')->textInput() ?>

    <?= $form->field($model, 'sr_co_relId')->textInput() ?>

    <?= $form->field($model, 'sr_ma_relId')->textInput() ?>

    <?= $form->field($model, 'sr_markObtained')->textInput() ?>

    <?= $form->field($model, 'sr_setBy')->textInput() ?>

    <?= $form->field($model, 'sr_setAt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
