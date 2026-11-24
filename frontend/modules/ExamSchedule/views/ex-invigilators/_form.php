<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ExamSchedule\models\ExInvigilators */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ex-invigilators-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <hiv class="hidden">
            <?= $form->field($model, 'exam_room_slot_relId')->textInput() ?>
            <?= $form->field($model, 'staffs')->textInput() ?>
        </hiv>
    </div>
    <div class="row">
        <div class="col-md-12 bg-info" style="text-align: center;">
            Exam Schedule : <b><u></u><?= $model->examRoomSlotRel->dateTimeSlotRel->getHeaderText() ?></u></b>&nbsp;&nbsp;
            Exam Room : <b><u></u><?= $model->examRoomSlotRel->classRoomRel->room ?></u></b>
            Date : <b><u></u><?= $model->examRoomSlotRel->dateTimeSlotRel->getDateScheduled() ?></u></b>
        </div>
        <div class="col-md-12">
            <h4 style="text-align: center;">Staff Members</h4>
            <?= $this->render('__report/satff-members-for-invigilator', ['model' => $model]) ?>
        </div>
    </div>
    <div class="row bg-info">
        <div class="col-md-10">
        Selected: <span id="selecteds_001">0</span>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success btn-xs']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<script>
    // window.saveForm = () => {
    //     alert('Gona save form...');

    // }
</script>