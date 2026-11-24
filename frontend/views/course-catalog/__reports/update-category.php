<?php

use backend\models\CourseCategory;
use backend\models\GradeScale;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\CourseCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="course-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'course_category_relId')->dropDownList(ArrayHelper::map(CourseCategory::find()->all(), 'id', 'catgeory_name'), ['prompt' => '--Select category--']) ?>

    <?= $form->field($model, 'min_pass_grade_relId')->dropDownList(ArrayHelper::map(GradeScale::find()
        ->where(['gs_scale_status' => 1, 'gs_forProgram' => $model->cocat_program_relId])
        ->orderBy(' gs_forProgram ASC, gs_gradePoint DESC ')
        ->all(), 'id', function ($model) {
        $gp = sprintf("%0.2f", $model->gs_gradePoint);
        return "{$model->gs_ltr_grade}|{$gp}|{$model->gs_pass_status}|{$model->gs_scale_status}|({$model->gsForProgram->program_name})";
    }), ['prompt' => '--Select grade--'])->hint('Letter grade|Grade Point|Pass Status|Active Status|(Program)') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>