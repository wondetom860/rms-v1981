<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\WithdrawalReadmission\models\WStudentReadmissionHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wstudent-readmission-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'srh_acadYear_relId')->textInput() ?>

    <?= $form->field($model, 'srh_sem_relId')->textInput() ?>

    <?= $form->field($model, 'srh_swh_relId')->textInput() ?>

    <?= $form->field($model, 'srh_std_relId')->textInput() ?>

    <?= $form->field($model, 'srh_return_back_on')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
