<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ContinuousAssessment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="continuous-assessment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ca_coff_relId')->textInput() ?>

    <?= $form->field($model, 'ca_semreg_relId')->textInput() ?>

    <?= $form->field($model, 'ca_std_relId')->textInput() ?>

    <?= $form->field($model, 'ca_assignment_relId')->textInput() ?>

    <?= $form->field($model, 'ca_markObtained')->textInput() ?>

    <?= $form->field($model, 'ca_created_at')->textInput() ?>

    <?= $form->field($model, 'ca_created_by')->textInput() ?>

    <?= $form->field($model, 'ca_updated_by')->textInput() ?>

    <?= $form->field($model, 'ca_updated_at')->textInput() ?>

    <?= $form->field($model, 'ca_approved')->textInput() ?>

    <?= $form->field($model, 'ca_approved_at')->textInput() ?>

    <?= $form->field($model, 'ca_approved_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
