<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration2 */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-registration2-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sr_academicYear_relId')->textInput() ?>

    <?= $form->field($model, 'sr_semester_relId')->textInput() ?>

    <?= $form->field($model, 'sr_student_relId')->textInput() ?>

    <?= $form->field($model, 'sr_courses')->textInput() ?>

    <?= $form->field($model, 'sr_status')->textInput() ?>

    <?= $form->field($model, 'sr_setOn')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
