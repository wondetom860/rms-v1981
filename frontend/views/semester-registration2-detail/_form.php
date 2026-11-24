<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration2Detail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-registration2-detail-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'srd2_semreg_relId')->textInput() ?>

    <?= $form->field($model, 'srd2_coff_relId')->textInput() ?>

    <?= $form->field($model, 'srd2_course_relId')->textInput() ?>

    <?= $form->field($model, 'srd2_grade_relId')->textInput() ?>

    <?= $form->field($model, 'srd2_coadd_flag')->textInput() ?>

    <?= $form->field($model, 'srd2_corptd_flag')->textInput() ?>

    <?= $form->field($model, 'srd2_codrpd_relId')->textInput() ?>

    <?= $form->field($model, 'srd2_corProject')->textInput() ?>

    <?= $form->field($model, 'srd2_enrollApproved')->textInput() ?>

    <?= $form->field($model, 'srd2_enrollApproved_by')->textInput() ?>

    <?= $form->field($model, 'srd2_enrolApprovrd_at')->textInput() ?>

    <?= $form->field($model, 'srd2_group_relId')->textInput() ?>

    <?= $form->field($model, 'srd2_updated_by')->textInput() ?>

    <?= $form->field($model, 'srd2_updated_at')->textInput() ?>

    <?= $form->field($model, 'srd2_created_at')->textInput() ?>

    <?= $form->field($model, 'srd2_created_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
