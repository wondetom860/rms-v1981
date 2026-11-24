<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ExamSchedule\models\ExExamRoomSlot */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ex-exam-room-slot-form">

    <?php $form = ActiveForm::begin(['action' => '/ExamSchedule/ex-exam-room-slot/create']); ?>
    <div class="hidden">
        <?= $form->field($model, 'date_time_slot_relId')->textInput() ?>
    </div>
    <div class="row">
        <div class="col-md-4">
            <label for="">Date Time Slot</label>
            <p class="bg-info badge"><?= $model->dateTimeSlotRel->getHeaderText() ?></p>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'class_room_relId')->dropDownList($model->getRoomList(), ['prompt' => 'Select Room']) ?>
        </div>
        <div class="col-md-2">
            <div class="form-group" style="margin-top: 10px;"><br>
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success btn-xs']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>