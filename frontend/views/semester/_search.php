<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'semester_name') ?>

    <?= $form->field($model, 'semester_status') ?>

    <?= $form->field($model, 'semester_desc') ?>

    <?= $form->field($model, 'semester_created_by') ?>

    <?php // echo $form->field($model, 'semester_created_at') ?>

    <?php // echo $form->field($model, 'semester_updated_by') ?>

    <?php // echo $form->field($model, 'semester_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
