<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\HonorListControlSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="honor-list-control-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'hl_maxSGPA') ?>

    <?= $form->field($model, 'hl_minSGPA') ?>

    <?= $form->field($model, 'hl_created_by') ?>

    <?= $form->field($model, 'hl_created_at') ?>

    <?php // echo $form->field($model, 'hl_updated_at') ?>

    <?php // echo $form->field($model, 'hl_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
