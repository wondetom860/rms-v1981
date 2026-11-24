<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\WithdrawalReadmission\models\WStudentWithdrawalHistorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wstudent-withdrawal-history-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'swh_acadYear_relId') ?>

    <?= $form->field($model, 'swh_sem_relId') ?>

    <?= $form->field($model, 'swh_std_relId') ?>

    <?= $form->field($model, 'swh_reason_relId') ?>

    <?php // echo $form->field($model, 'swh_course_removed') ?>

    <?php // echo $form->field($model, 'swh_approved') ?>

    <?php // echo $form->field($model, 'swh_approved_by') ?>

    <?php // echo $form->field($model, 'swh_approved_at') ?>

    <?php // echo $form->field($model, 'swh_created_at') ?>

    <?php // echo $form->field($model, 'swh_created_by') ?>

    <?php // echo $form->field($model, 'swh_updated_at') ?>

    <?php // echo $form->field($model, 'swh_updated_by') ?>

    <?php // echo $form->field($model, 'swh_HOD_approved') ?>

    <?php // echo $form->field($model, 'swh_HOD_approved_at') ?>

    <?php // echo $form->field($model, 'swh_HOD_approved_by') ?>

    <?php // echo $form->field($model, 'swh_returnedBack') ?>

    <?php // echo $form->field($model, 'swh_stdSigned') ?>

    <?php // echo $form->field($model, 'swh_stdSigned_at') ?>

    <?php // echo $form->field($model, 'swh_stdSigned_by') ?>

    <?php // echo $form->field($model, 'swh_lastApproved') ?>

    <?php // echo $form->field($model, 'swh_lastApproved_at') ?>

    <?php // echo $form->field($model, 'swh_lastApproved_by') ?>

    <?php // echo $form->field($model, 'swh_supDoc') ?>

    <?php // echo $form->field($model, 'swh_last_dateAttended') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
