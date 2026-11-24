<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\GraduateStudentPointHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="graduate-student-point-history-form">

    <?php $form = ActiveForm::begin(['action' => '/graduate-student-point-history/create','options' => ['onsubmit' => 'submitForm($(this));return false;']]); ?>

    <div class="row">
        <div class="col-md-3">
            <!-- gph_registration_entry_relId -->
            <?= $form->field($model, 'acadYear')->textInput(['readOnly' => true, 'value' => $model->gphRegistrationEntryRel->gphr_acadYear]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'gph_SGPA')->textInput() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'gph_CGPA')->textInput() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'gph_credit_hour_enrolled')->textInput() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'gph_average_point')->textInput(['id' => 'graduate-student-point-history-acadYear'.$model->gph_average_point]) ?>
        </div>
        <div class="col-md-1">
            <div class="form-group pull-right"><br>
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
                <?php echo Html::activeHiddenInput($model, "gph_registration_entry_relId");?>
                <?php echo Html::activeHiddenInput($model, "gph_gstd_relId");?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    window.submitForm = function(formm){
        // var \$form = formm;
        stdId = $("#graduatestudentpointhistory-gph_gstd_relid").val();
        $.post(
            formm.attr('action'),
            formm.serialize(),
            function(result){
                if(result == 1){
                    loadGradeHistoryForm(stdId);
                    // coffLoad();
                    }else{
                        alert(result);
                    }
                }
            );
        return false;
    }
</script>
