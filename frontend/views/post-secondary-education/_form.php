<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\PostSecondaryEducation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="post-secondary-education-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pse_ei_relId')->textInput() ?>

    <?= $form->field($model, 'pse_institute_name')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pse_nationality_relId')->textInput() ?>

    <?= $form->field($model, 'pse_from_year')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pse_to_year')->textInput() ?>

    <?= $form->field($model, 'pse_credit_earned')->textInput() ?>

    <?= $form->field($model, 'pse_CGPA_obtained')->textInput() ?>

    <?= $form->field($model, 'pse_certificate_earned')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pse_created_by')->textInput() ?>

    <?= $form->field($model, 'pse_updated_by')->textInput() ?>

    <?= $form->field($model, 'pse_created_at')->textInput() ?>

    <?= $form->field($model, 'pse_updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
