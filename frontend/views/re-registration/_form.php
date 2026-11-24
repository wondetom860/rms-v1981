<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\ReRegistration */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="re-registration-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'prev_semreg')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'prev_srd2')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'std_relId')->textInput() ?>

    <?= $form->field($model, 'prev_gas_relId')->textInput() ?>

    <?= $form->field($model, 'new_gas_relId')->textInput() ?>

    <?= $form->field($model, 'course_relId')->textInput() ?>

    <?= $form->field($model, 'new_coff2det_relId')->textInput() ?>

    <?= $form->field($model, 'trail_count')->textInput() ?>

    <?= $form->field($model, 'reg_approved')->textInput() ?>

    <?= $form->field($model, 'reg_approved_at')->textInput() ?>

    <?= $form->field($model, 'reg_approved_by')->textInput() ?>

    <?= $form->field($model, 'dept_approved')->textInput() ?>

    <?= $form->field($model, 'dept_approved_at')->textInput() ?>

    <?= $form->field($model, 'dept_approved_by')->textInput() ?>

    <?= $form->field($model, 'admin_processed')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'deleted_at')->textInput() ?>

    <?= $form->field($model, 'deleted_by')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
