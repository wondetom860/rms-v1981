<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluationDetailSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-evaluation-detail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'semevdet_semev_relId') ?>

    <?= $form->field($model, 'semevdet_evalType_relId') ?>

    <?= $form->field($model, 'semevdet_completed') ?>

    <?= $form->field($model, 'semevdet_averagePoint') ?>

    <?php // echo $form->field($model, 'semevdet_evaluee_relId') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
