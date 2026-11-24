<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ReRegistrationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="re-registration-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'prev_semreg') ?>

    <?= $form->field($model, 'prev_srd2') ?>

    <?= $form->field($model, 'std_relId') ?>

    <?= $form->field($model, 'prev_gas_relId') ?>

    <?php // echo $form->field($model, 'new_gas_relId') ?>

    <?php // echo $form->field($model, 'course_relId') ?>

    <?php // echo $form->field($model, 'new_coff2det_relId') ?>

    <?php // echo $form->field($model, 'trail_count') ?>

    <?php // echo $form->field($model, 'reg_approved') ?>

    <?php // echo $form->field($model, 'reg_approved_at') ?>

    <?php // echo $form->field($model, 'reg_approved_by') ?>

    <?php // echo $form->field($model, 'dept_approved') ?>

    <?php // echo $form->field($model, 'dept_approved_at') ?>

    <?php // echo $form->field($model, 'dept_approved_by') ?>

    <?php // echo $form->field($model, 'admin_processed') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'deleted_at') ?>

    <?php // echo $form->field($model, 'deleted_by') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'data') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
