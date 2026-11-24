<?php

use frontend\modules\AcademicCalendar\models\AcAcademicYear;
use kartik\date\DatePicker;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ExamSchedule\models\ExExamScheduleEntry */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ex-exam-schedule-entry-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'acad_year_relId')->dropdownList(ArrayHelper::map(AcAcademicYear::find()
                ->all(), 'id', function ($model) {
                return $model->getAcHeader();
            }), ['prompt' => 'Select Academic Year']) ?>
        </div>
        <div class="col-md-8">
            <?= $form->field($model, 'header_text')->textarea(['rows' => 1]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'start_date_str')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'autoClose' => true,
                    'format' => 'dd-m-yyyy',
                ],
            ]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'end_date_str')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'autoClose' => true,
                    'format' => 'dd-m-yyyy',
                ],
            ]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>