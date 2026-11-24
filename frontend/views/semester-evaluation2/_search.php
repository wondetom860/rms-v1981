<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluation2Search */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-evaluation2-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'semev_acadYear_relId') ?>

    <?= $form->field($model, 'semev_sem_relId') ?>

    <?= $form->field($model, 'semev_group_relId') ?>

    <?= $form->field($model, 'semev_course_relId') ?>

    <?php // echo $form->field($model, 'semev_evaluee_relId') ?>

    <?php // echo $form->field($model, 'semev_evalType_relId') ?>

    <?php // echo $form->field($model, 'semev_averagePoint') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
