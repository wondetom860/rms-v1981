<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WEvaluationEntryTableSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wevaluation-entry-table-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'eet_et_relId') ?>

    <?= $form->field($model, 'eet_evaluator_role') ?>

    <?= $form->field($model, 'eet_reviewee_role') ?>

    <?= $form->field($model, 'eet_max_point') ?>

    <?php // echo $form->field($model, 'eet_created_by') ?>

    <?php // echo $form->field($model, 'eet_created_at') ?>

    <?php // echo $form->field($model, 'eet_updated_at') ?>

    <?php // echo $form->field($model, 'eet_updated_by') ?>

    <?php // echo $form->field($model, 'data') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decris', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decris', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
