<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\GraduationRequirmentControlSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="graduation-requirment-control-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'gr_program_relId') ?>

    <?= $form->field($model, 'gr_maj') ?>

    <?= $form->field($model, 'gr_min') ?>

    <?= $form->field($model, 'gr_created_at') ?>

    <?php // echo $form->field($model, 'gr_created_by') ?>

    <?php // echo $form->field($model, 'gr_updated_at') ?>

    <?php // echo $form->field($model, 'gr_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
