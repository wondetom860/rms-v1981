<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcademicCalendar\models\AcActivePrograms */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ac-active-programs-form">

    <?php $form = ActiveForm::begin(); ?>
    
    <div class="row" style="background-color: white;">
        <div class="col-md-6">
            <div class="row">
                <div class="col-md-3">
                    <?= $form->field($model, 'academic_year_relId')->textInput(['readOnly' => true, 'title' => $model->academicYearRel->getAcHeader()])->hint($model->academicYearRel->getAcHeader()); ?>
                </div>
                <div class="col-md-4">
                     <?= $form->field($model, 'admission_relId')->dropDownList(ArrayHelper::map(backend\models\Admission::find()->all(),'id','admission_name'), ['prompt' => '']) ?>
                </div>
                <div class="col-md-5">
                    <?= $form->field($model, 'program_relId')->dropDownList(ArrayHelper::map(backend\models\Program::find()->all(),'id','program_name'), 
                        [
                            'prompt' => '',
                            'onChange' => ' $.post("/program/clslvllistss",{progId: $(this).val()}).done((resp) => {
                                $("#acactiveprograms-class_level_relid").html(resp).trigger("change");
                                $("#acactiveprograms-dept_relid").trigger("change");
                            }) '
                        ]) 
                    ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <?= $form->field($model, 'class_level_relId')->dropDownList(ArrayHelper::map(backend\models\ClassLevel::find()->all(),'id',function($model){ return $model->clslvl_num; }), ['prompt' => '']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'dept_relId')->dropDownList(ArrayHelper::map(backend\models\Department::find()->all(),'id','dept_name'), 
                        [
                            'prompt' => '',
                            'onChange' => ' $.post("/specialization/listspec-cp",{
                                deptId: $(this).val(),
                                progId:$("#acactiveprograms-program_relid").val(),
                                admId:$("#acactiveprograms-admission_relid").val()
                            }).done((resp) => $("#acactiveprograms-spec_relid").html(resp)); '
                        ]) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'semester_relId')->dropDownList(ArrayHelper::map(backend\models\Semester::find()->all(),'id','semester_name'), ['prompt' => '']) ?>
                </div>
                <div class="col-md-6">
                    <?= $form->field($model, 'spec_relId')->dropDownList(ArrayHelper::map(backend\models\Specialization::find()->all(),'id','spec_name'), ['prompt' => '']) ?>
                </div>
                <div class="col-md-12">
                    <?= $form->field($model, 'remark')->textarea(['rows' => 1]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'graduate_class')->checkbox() ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'on_internship')->checkbox() ?>
                </div>
                <div class="col-md-3"><br>
                    <div class="form-group pull-right">
                        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
                    </div>
                </div>
                
                
            </div>
        </div>
        <div class="col-md-6" style="font-family: cursive; font-size: 8pt; max-height: 450px; overflow: auto;">
            <?= $this->render('__report/index',['records' => $records,'ayId' => $model->academic_year_relId]) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
