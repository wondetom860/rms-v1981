<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/** @var frontend\modules\TRANSTUD\models\ExemptedCourse  $model*/
/* @var $form yii\widgets\ActiveForm */
?>

<div class="exempted-course-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'course_code')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'course_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'course_hours')->textInput(["onchange" => "calculateGps();return false;"]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'grade_obtained')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'grade_point')->textInput(['maxlength' => true,"onchange" => "calculateGps();return false;"]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'grade_point_sum')->textInput(['readonly' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-7">
            <div class="form-group">
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success pull-right']) ?>
                <?= Html::activeHiddenInput($model, 'ts_relid', ['value' => $model->ts_relid]) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>
    <script>
        function calculateGps() {
            var courseHours = parseFloat($('#exemptedcourse-course_hours').val());
            var gradePoint = parseFloat($('#exemptedcourse-grade_point').val());
            if (!isNaN(courseHours) && !isNaN(gradePoint)) {
                var gradePointSum = courseHours * gradePoint;
                $('#exemptedcourse-grade_point_sum').val(gradePointSum.toFixed(2));
            } else {
                $('#exemptedcourse-grade_point_sum').val('');
            }
        }
    </script>
</div>