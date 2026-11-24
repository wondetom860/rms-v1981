<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseCriteriaBundleSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ase-criteria-bundle-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'code') ?>

    <?= $form->field($model, 'eval_type_relId') ?>

    <?= $form->field($model, 'evaluator_role') ?>

    <?= $form->field($model, 'candidate_staff_role') ?>

    <?php // echo $form->field($model, 'average_point') ?>

    <?php // echo $form->field($model, 'average_percentage') ?>

    <?php // echo $form->field($model, 'evaluate_per_course') ?>

    <?php // echo $form->field($model, 'criteria_IDS') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'data') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
