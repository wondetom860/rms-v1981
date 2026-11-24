<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistrationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-registration-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sr_student_relId') ?>

    <?= $form->field($model, 'sr_co_relId') ?>

    <?= $form->field($model, 'sr_ma_relId') ?>

    <?= $form->field($model, 'sr_markObtained') ?>

    <?php // echo $form->field($model, 'sr_setBy') ?>

    <?php // echo $form->field($model, 'sr_setAt') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
