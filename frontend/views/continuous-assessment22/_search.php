<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ContinuousAssessmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="continuous-assessment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ca_coff_relId') ?>

    <?= $form->field($model, 'ca_semreg_relId') ?>

    <?= $form->field($model, 'ca_std_relId') ?>

    <?= $form->field($model, 'ca_assignment_relId') ?>

    <?php // echo $form->field($model, 'ca_markObtained') ?>

    <?php // echo $form->field($model, 'ca_created_at') ?>

    <?php // echo $form->field($model, 'ca_created_by') ?>

    <?php // echo $form->field($model, 'ca_updated_by') ?>

    <?php // echo $form->field($model, 'ca_updated_at') ?>

    <?php // echo $form->field($model, 'ca_approved') ?>

    <?php // echo $form->field($model, 'ca_approved_at') ?>

    <?php // echo $form->field($model, 'ca_approved_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
