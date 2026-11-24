<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ContAssessMarkAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cont-assess-mark-assignment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ma_catagory_relId') ?>

    <?= $form->field($model, 'ma_instance') ?>

    <?= $form->field($model, 'ma_possiblePoint') ?>

    <?= $form->field($model, 'ma_created_at') ?>

    <?php // echo $form->field($model, 'ma_created_by') ?>

    <?php // echo $form->field($model, 'ma_updated_by') ?>

    <?php // echo $form->field($model, 'ma_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
