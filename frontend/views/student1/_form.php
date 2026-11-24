<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\Student1 */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="student1-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'std_user_relId')->textInput() ?>

    <?= $form->field($model, 'std_regId')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'std_type')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'std_dept_relId')->textInput() ?>

    <?= $form->field($model, 'std_spec_relId')->textInput() ?>

    <?= $form->field($model, 'std_clslvl_relId')->textInput() ?>

    <?= $form->field($model, 'std_prog_relId')->textInput() ?>

    <?= $form->field($model, 'std_schlrshpType_relId')->textInput() ?>

    <?= $form->field($model, 'std_admission_relId')->textInput() ?>

    <?= $form->field($model, 'std_acadYear_relId')->textInput() ?>

    <?= $form->field($model, 'std_group_realId')->textInput() ?>

    <?= $form->field($model, 'std_semester_status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'std_marital_status')->dropDownList([ 'Single' => 'Single', 'Maried' => 'Maried', 'Divorced' => 'Divorced', 'Widowed' => 'Widowed', 'Other' => 'Other', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'std_emergency_contact_relId')->textInput() ?>

    <?= $form->field($model, 'st_educ_info_relId')->textInput() ?>

    <?= $form->field($model, 'std_created_at')->textInput() ?>

    <?= $form->field($model, 'std_created_by')->textInput() ?>

    <?= $form->field($model, 'std_updated_at')->textInput() ?>

    <?= $form->field($model, 'std_updated_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
