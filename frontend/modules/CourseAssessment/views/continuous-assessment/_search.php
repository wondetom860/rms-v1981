<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\CourseAssessment\models\ContinuousAssessmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="continuous-assessment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ca_std_relid') ?>

    <?= $form->field($model, 'ca_semreg_relId') ?>

    <?= $form->field($model, 'ca_markAssignment_relId') ?>

    <?= $form->field($model, 'ca_catagorySetup_relId') ?>

    <?php // echo $form->field($model, 'ca_markObtained') ?>

    <?php // echo $form->field($model, 'ca_coff_relId') ?>

    <?php // echo $form->field($model, 'ca_created_by') ?>

    <?php // echo $form->field($model, 'ca_created_at') ?>

    <?php // echo $form->field($model, 'ca_updated_by') ?>

    <?php // echo $form->field($model, 'ca_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
