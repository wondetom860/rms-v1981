<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\DepartmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="department-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'dept_name') ?>

    <?= $form->field($model, 'dept_location') ?>

    <?= $form->field($model, 'dept_phone') ?>

    <?php // echo $form->field($model, 'dept_code') ?>

    <?php // echo $form->field($model, 'dept_created_by') ?>

    <?php // echo $form->field($model, 'dept_created_at') ?>

    <?php // echo $form->field($model, 'dept_updated_at') ?>

    <?php // echo $form->field($model, 'dept_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
