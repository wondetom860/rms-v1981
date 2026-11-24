<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WSemesterEvaluationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wsemester-evaluation-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'se_eet_relId') ?>

    <?= $form->field($model, 'se_reviewee_relId') ?>

    <?= $form->field($model, 'se_total_point_given') ?>

    <?= $form->field($model, 'se_date_started') ?>

    <?php // echo $form->field($model, 'se_acadYear_relId') ?>

    <?php // echo $form->field($model, 'se_sem_relId') ?>

    <?php // echo $form->field($model, 'se_coff2det_relId') ?>

    <?php // echo $form->field($model, 'data') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decris', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decris', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
