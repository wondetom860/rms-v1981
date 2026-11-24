<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\GradeScaleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="grade-scale-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'gs_min') ?>

    <?= $form->field($model, 'gs_max') ?>

    <?= $form->field($model, 'gs_ltr_grade') ?>

    <?= $form->field($model, 'gs_gradePoint') ?>

    <?php // echo $form->field($model, 'gs_remark') ?>

    <?php // echo $form->field($model, 'gs_created_by') ?>

    <?php // echo $form->field($model, 'gs_created_at') ?>

    <?php // echo $form->field($model, 'gs_updated_by') ?>

    <?php // echo $form->field($model, 'gs_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
