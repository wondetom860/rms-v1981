<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WEvaluationPointsSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wevaluation-points-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ep_desc') ?>

    <?= $form->field($model, 'ep_ceated_at') ?>

    <?= $form->field($model, 'ep_created_by') ?>

    <?= $form->field($model, 'ep_updated_at') ?>

    <?php // echo $form->field($model, 'ep_updated_by') ?>

    <?php // echo $form->field($model, 'data') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decris', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decris', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
