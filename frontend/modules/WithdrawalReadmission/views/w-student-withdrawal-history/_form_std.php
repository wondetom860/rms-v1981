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

    <?= $form->field($model, 'swh_stdSigned')->textInput() ?>

    <?= $form->field($model, 'swh_last_dateAttended')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
