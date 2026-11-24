<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\FacultySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="faculty-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'fac_name') ?>

    <?= $form->field($model, 'fac_description') ?>

    <?= $form->field($model, 'fac_created_by') ?>

    <?= $form->field($model, 'fac_updated_by') ?>

    <?php // echo $form->field($model, 'fac_created_at') ?>

    <?php // echo $form->field($model, 'fac_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
