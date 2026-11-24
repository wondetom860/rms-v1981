<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluationScaleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-evaluation-scale-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ses_name') ?>

    <?= $form->field($model, 'ses_maxPoint') ?>

    <?= $form->field($model, 'ses_minPoint') ?>

    <?= $form->field($model, 'remark') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
