<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ThesisTitleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="thesis-title-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'tt_srd2_relId') ?>

    <?= $form->field($model, 'tt_std_relId') ?>

    <?= $form->field($model, 'tt_title') ?>

    <?= $form->field($model, 'tt_approved') ?>

    <?php // echo $form->field($model, 'tt_approved_by') ?>

    <?php // echo $form->field($model, 'tt_approved_at') ?>

    <?php // echo $form->field($model, 'tt_hod_approved') ?>

    <?php // echo $form->field($model, 'tt_hod_approved_at') ?>

    <?php // echo $form->field($model, 'tt_hod_approved_by') ?>

    <?php // echo $form->field($model, 'tt_graded') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
