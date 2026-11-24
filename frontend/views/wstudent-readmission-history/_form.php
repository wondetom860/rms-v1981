<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\WStudentReadmissionHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wstudent-readmission-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'srh_acadYear_relId')->textInput() ?>

    <?= $form->field($model, 'srh_sem_relId')->textInput() ?>

    <?= $form->field($model, 'srh_swh_relId')->textInput() ?>

    <?= $form->field($model, 'srh_std_relId')->textInput() ?>

    <?= $form->field($model, 'srh_return_back_on')->textInput() ?>

    <?= $form->field($model, 'srh_HOD_approved')->textInput() ?>

    <?= $form->field($model, 'srh_HOD_approved_at')->textInput() ?>

    <?= $form->field($model, 'srh_HOD_approved_by')->textInput() ?>

    <?= $form->field($model, 'srh_approved')->textInput() ?>

    <?= $form->field($model, 'srh_approved_at')->textInput() ?>

    <?= $form->field($model, 'srh_approved_by')->textInput() ?>

    <?= $form->field($model, 'srh_return_back_letter')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'remark_HOD')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'remark_registrar')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
