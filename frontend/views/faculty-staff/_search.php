<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\FacultyStaffSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="faculty-staff-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'fas_faculty_relId') ?>

    <?= $form->field($model, 'fas_roleName') ?>

    <?= $form->field($model, 'fas_user_relId') ?>

    <?= $form->field($model, 'fas_assigned_at') ?>

    <?php // echo $form->field($model, 'fas_assigned_by') ?>

    <?php // echo $form->field($model, 'fas_status') ?>

    <?php // echo $form->field($model, 'fas_updated_at') ?>

    <?php // echo $form->field($model, 'fas_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
