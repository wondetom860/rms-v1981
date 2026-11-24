<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ReassessmentHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reassessment-history-form">

    <?php $form = ActiveForm::begin(['action' => '/reassessment-history/update-mark?id='.$model->id]); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'first_name')->textInput([
                'readonly' => true,
                'value' => $model->rasStdRel->getFullName(),
            ]) ?>
        </div>
        <div class="col-md-6"><br><br>
            <b>Year of Admission:</b> <?= $model->rasSrd2Rel->srd2SemregRel->semregAcadYearRel->sch_year_desc."-".$model->rasSrd2Rel->srd2SemregRel->semregSemesterRel->semester_name ?>
            <br>
            <div class="hidden">
                <?= $form->field($model, 'id')->textInput() ?>
            </div>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'course_title')->textInput([
                'readonly' => true,
                'value' => $model->rasCourseRel->course_title."[{$model->rasCourseRel->getCourseCredits()}]",
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'beforeMark')->textInput([
                'readonly' => true,
                'value' => $model->ras_prevMark."/{$model->rasContAssCatagRel->cat_maxPoint}",
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'totalMark')->textInput([
                'readonly' => true,
                'value' => $model->rasSrd2Rel->getTotalMark(),
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'beforeGrade')->textInput([
                'readonly' => true,
                'value' => $model->ras_bef_grade_relId ? $model->rasBefGradeRel->gs_ltr_grade : "-",
            ]) ?>
        </div>
        
        
        <div class="col-md-6">
            <?= $form->field($model, 'ras_afterMark')->textInput(['max' => $model->rasContAssCatagRel->cat_maxPoint])->hint("Enter Mark Obtained After Correction out of {$model->rasContAssCatagRel->cat_maxPoint}") ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'afterGrade')->textInput([
                'readonly' => true,
                'value' => $model->ras_aft_grade_relId ? $model->rasAftGradeRel->gs_ltr_grade : "-",
            ]) ?>
        </div>
        
    </div>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success','onclick' => 'submitForm();return false;']) ?>
    </div>

    <script type="text/javascript">
        window.submitForm = ()=>{
            modelId = $("#reassessmenthistory-id").val();
            max = Number($("#reassessmenthistory-ras_aftermark").attr('max'));
            afterMark = Number($("#reassessmenthistory-ras_aftermark").val());

            console.log(afterMark,max);
            if (afterMark > max) {
                // $("#reassessmenthistory-ras_aftermark").addError('Mark can Not be more that '+max);
                $("#reassessmenthistory-ras_aftermark").focus();
            }else{
                $.post('/reassessment-history/update-mark',{id:modelId,afterMark:afterMark}).done((res)=>{ if (res == 1) {  }else{ alert(res); } });
            }            
            return false;
        }
    </script>
    <?php ActiveForm::end(); ?>

</div>
