<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ExamSchedule\models\ExInvAttendanceForm */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ex-inv-attendance-form-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'date_time_slot_relId')->textInput() ?>

    <?= $form->field($model, 'exam_slot_relId')->textInput() ?>

    <?= $form->field($model, 'inviglator_relId')->textInput() ?>

    <?= $form->field($model, 'assigned_as')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'status')->textInput() ?>

    <?= $form->field($model, 'approved')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
