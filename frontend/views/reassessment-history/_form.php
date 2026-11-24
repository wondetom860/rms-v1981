<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ReassessmentHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reassessment-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ras_std_relId')->textInput() ?>

    <?= $form->field($model, 'ras_srd2_relId')->textInput() ?>

    <?= $form->field($model, 'ras_course_relId')->textInput() ?>

    <?= $form->field($model, 'ras_acadYear_relId')->textInput() ?>

    <?= $form->field($model, 'ras_sem_relId')->textInput() ?>

    <?= $form->field($model, 'ras_group_relId')->textInput() ?>

    <?= $form->field($model, 'ras_clslvl_relId')->textInput() ?>

    <?= $form->field($model, 'ras_status')->textInput() ?>

    <?= $form->field($model, 'ras_approved_by')->textInput() ?>

    <?= $form->field($model, 'ras_bef_grade_relId')->textInput() ?>

    <?= $form->field($model, 'ras_app_date')->textInput() ?>

    <?= $form->field($model, 'ras_created_at')->textInput() ?>

    <?= $form->field($model, 'ras_updated_at')->textInput() ?>

    <?= $form->field($model, 'ras_updated_by')->textInput() ?>

    <?= $form->field($model, 'ras_aft_grade_relId')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
