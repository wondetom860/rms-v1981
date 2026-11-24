<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\RemovedSrd2Search */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="removed-srd2-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'semreg_relId') ?>

    <?= $form->field($model, 'std_relId') ?>

    <?= $form->field($model, 'srd2_serialized') ?>

    <?= $form->field($model, 'cont_ass_marks') ?>

    <?php // echo $form->field($model, 'total_mark') ?>

    <?php // echo $form->field($model, 'grade_relId') ?>

    <?php // echo $form->field($model, 'hod_approved') ?>

    <?php // echo $form->field($model, 'hod_approved_at') ?>

    <?php // echo $form->field($model, 'hod_approved_by') ?>

    <?php // echo $form->field($model, 'reg_approved') ?>

    <?php // echo $form->field($model, 'reg_approved_at') ?>

    <?php // echo $form->field($model, 'reg_approved_by') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'data') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
