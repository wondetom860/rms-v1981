<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration2Search */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-registration2-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'semreg_std_relId') ?>

    <?= $form->field($model, 'semreg_acadYear_relId') ?>

    <?= $form->field($model, 'semreg_clslvl_relId') ?>

    <?= $form->field($model, 'semreg_semester_relId') ?>

    <?php // echo $form->field($model, 'semreg_SGPA') ?>

    <?php // echo $form->field($model, 'semreg_CGPA') ?>

    <?php // echo $form->field($model, 'semreg_approved') ?>

    <?php // echo $form->field($model, 'semreg_approved_by') ?>

    <?php // echo $form->field($model, 'semreg_approved_at') ?>

    <?php // echo $form->field($model, 'semreg_created_by') ?>

    <?php // echo $form->field($model, 'semreg_updated_by') ?>

    <?php // echo $form->field($model, 'semreg_created_at') ?>

    <?php // echo $form->field($model, 'semreg_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
