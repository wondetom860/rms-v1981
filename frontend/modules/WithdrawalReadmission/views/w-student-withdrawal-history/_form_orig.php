<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\WithdrawalReadmission\models\WStudentWithdrawalHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wstudent-withdrawal-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'swh_acadYear_relId')->textInput() ?>

    <?= $form->field($model, 'swh_sem_relId')->textInput() ?>

    <?= $form->field($model, 'swh_std_relId')->textInput() ?>

    <?= $form->field($model, 'swh_reason_relId')->textInput() ?>

    <?= $form->field($model, 'swh_course_removed')->textInput() ?>

    <?= $form->field($model, 'swh_approved')->textInput() ?>

    <?= $form->field($model, 'swh_approved_by')->textInput() ?>

    <?= $form->field($model, 'swh_approved_at')->textInput() ?>

    <?= $form->field($model, 'swh_created_at')->textInput() ?>

    <?= $form->field($model, 'swh_created_by')->textInput() ?>

    <?= $form->field($model, 'swh_updated_at')->textInput() ?>

    <?= $form->field($model, 'swh_updated_by')->textInput() ?>

    <?= $form->field($model, 'swh_HOD_approved')->textInput() ?>

    <?= $form->field($model, 'swh_HOD_approved_at')->textInput() ?>

    <?= $form->field($model, 'swh_HOD_approved_by')->textInput() ?>

    <?= $form->field($model, 'swh_returnedBack')->textInput() ?>

    <?= $form->field($model, 'swh_stdSigned')->textInput() ?>

    <?= $form->field($model, 'swh_stdSigned_at')->textInput() ?>

    <?= $form->field($model, 'swh_stdSigned_by')->textInput() ?>

    <?= $form->field($model, 'swh_lastApproved')->textInput() ?>

    <?= $form->field($model, 'swh_lastApproved_at')->textInput() ?>

    <?= $form->field($model, 'swh_lastApproved_by')->textInput() ?>

    <?= $form->field($model, 'swh_supDoc')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'swh_last_dateAttended')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
