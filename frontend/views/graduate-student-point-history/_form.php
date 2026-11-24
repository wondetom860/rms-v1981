<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/* @var $model backend\models\GraduateStudentPointHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="graduate-student-point-history-form">
    <div class="row">
        <div class="col-md-12">
            <?php $form = ActiveForm::begin(); ?>
            <?= $form->field($model, 'gph_gstd_relId')->widget(Select2::class, [
                'data' => ArrayHelper::map(\backend\models\GraduateStudent::find()->all(), 'id', function($model){
                    $user = $model->gstdUserRel;
                    return $user->first_name.' '. $user->middle_name.' '.$user->last_name.' ('.$model->gstdProgRel->program_name.')';
                }),
                'options' => [
                    'placeholder' => 'Select Student',
                    'onchange' =>'
                        loadGradeHistoryForm($(this).val());return false;
                    ',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                      ],
                  ]); 
            ?>
            <?php ActiveForm::end(); ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" id="GraduationHistoryContainer" style="padding: 5px; border: 1px solid black;">
            
        </div>
        <div class="col-md-12" id="gradePointHistoryContainer" style="font-size: 10pt; border: 1px solid black;">
            
        </div>
    </div>

</div>

<script type="text/javascript">
    var gstdId1;
    window.loadGradeHistoryForm = (gstdId) =>{
        gstdId1 = gstdId;
        $('#gradePointHistoryContainer').html('Loading Form...');
        $('#GraduationHistoryContainer').html('Loading Form...');

        $.post('/graduate-student/get-record',{recordId:gstdId}).done((data) => {
            $('#GraduationHistoryContainer').html(data);
        });

        $.post('/graduate-student-point-history/get-form',{gstdId:gstdId}).done((data) => {$('#gradePointHistoryContainer').html(data);});

        $('.graduate-student-point-history-form').each(function(){
            console.log(this);
        });
    }
</script>
