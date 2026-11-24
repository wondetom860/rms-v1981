<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WSeEvaluatorsList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wse-evaluators-list-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sel_se_relId')->textInput() ?>

    <?= $form->field($model, 'sel_evaluator_relId')->textInput() ?>

    <?= $form->field($model, 'se_date_started')->textInput() ?>

    <?= $form->field($model, 'se_date_finished')->textInput() ?>

    <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decris', 'Create') : Yii::t('decris', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
