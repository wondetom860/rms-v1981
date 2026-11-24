<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ClearanceAllowedStudentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="clearance-allowed-student-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cas_full_name') ?>

    <?= $form->field($model, 'cas_regId') ?>

    <?= $form->field($model, 'cas_acadYear') ?>

    <?= $form->field($model, 'cas_semester') ?>

    <?php // echo $form->field($model, 'cas_admission') ?>

    <?php // echo $form->field($model, 'cas_program') ?>

    <?php // echo $form->field($model, 'cas_department') ?>

    <?php // echo $form->field($model, 'cas_clslvl') ?>

    <?php // echo $form->field($model, 'cas_spec') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
