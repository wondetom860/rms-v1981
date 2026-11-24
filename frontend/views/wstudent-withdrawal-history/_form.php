<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\WStudentWithdrawalHistory */
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

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
