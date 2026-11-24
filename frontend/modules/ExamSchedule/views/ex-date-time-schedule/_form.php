<?php

use frontend\modules\ExamSchedule\models\ExExamScheduleEntry;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ExamSchedule\models\ExDateTimeSchedule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ex-date-time-schedule-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'exam_schedule_entry_relId')->dropDownList(ArrayHelper::map(ExExamScheduleEntry::find()->all(), 'id', function ($model) {
                return $model->getScehduleEntryHeader();
            })) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'shift')
                ->dropDownList(['MORNING' => 'MORNING', 'AFTERNOON' => 'AFTERNOON', 'UKNOWN' => 'UKNOWN']) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'date_str')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'autoClose' => true,
                    'format' => 'dd-m-yyyy',
                ],
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'start_time_str')->textInput(['type' => 'time']) ?>

        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'end_time_str')->textInput(['type' => 'time']) ?>

        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success', 'option' => "handleSubmitForm();return false;"]) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    function handleSubmitForm() {
        var date = $('#exdatetimeschedule-date').val();
        var start_time = $('#exdatetimeschedule-start_time').val();
        var end_time = $('#exdatetimeschedule-end_time').val();
        var data = {
            date: date,
            start_time: start_time,
            end_time: end_time
        };
        $.ajax({
            url: 'index.php?r=ExamSchedule/ex-date-time-schedule/create',
            type: 'POST',
            data: data,
            success: function(data) {
                console.log(data);
            }
        });
    }
</script>