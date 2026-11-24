<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/* @var $model backend\models\Student */
/* @var $form yii\widgets\ActiveForm */

$model = &$sessionForm['models']['modelStudent'];


?>

<div class="student-form">

    <?php $form = ActiveForm::begin(['id'=>'modelStudent']); ?>
    <div class="hidden">
        <?= $form->field($model,'std_user_relId') ?>
        <?= $form->field($model,'std_admission_relId') ?>
        <?= $form->field($model,'std_prog_relId') ?>
        <?= $form->field($model,'std_dept_relId') ?>
        <?= $form->field($model,'std_clslvl_relId') ?>
        <?= $form->field($model,'std_spec_relId') ?>
        <?= $form->field($model,'std_acProgSched_relId') ?>
        <?= $form->field($model,'std_acadYear_relId') ?>
        <?= $form->field($model,'std_sem_relId') ?>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Student academic profile 
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <?= $form->field($model, 'std_schlrshpType_relId')->dropDownList(
                                    ArrayHelper::map(
                                            backend\models\ScholarshipType::find()->all(), 'id', 'schType_name'
                                    ),
                                    ['prompt' => 'Select Sponsorship Type', ]
                                )?>
                            </div>

                            <div class="col-md-3">
                                <?= $form->field($model, 'entry_type')->radioList(
                                        [
                                            1=>' Regular ',
                                            2=>' Transfer ',
                                            3=>' Special ',
                                        ],
                                        [
                                            'title'=>'Entry type',
                                            'value'=>'Regular',
                                        ]
                                    )->label('Entry Type')?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'std_marital_status')->radioList(
                                        [
                                            'SINGLE'=>' Single ',
                                            'MARRIED'=>' Married ',
                                            'Other'=>' Other ',
                                        ],
                                        [
                                            'title'=>'Select Marital Status',
                                            'value'=>'SINGLE',
                                        ]
                                    )->label('Marital Status')?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-12 pull-right">
                                <div class="form-group pull-right">
                                    <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Save and Continue') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    selectedDepts = [];
    selectedDeptNames = [];
    $("#modelStudent input,select").change(function(e){
        $('#modelStudent .btn').prop('disabled',false);
    });
    $("#modelStudent").on('submit',function(e){
        e.preventDefault();
        ak = submitMaster($("#modelStudent")[0].id);
        if (ak == 1) {
            $('#modelStudent .btn').prop('disabled',true);
        }
        // if (selectedDepts.length) {
        //     $("#student1-std_deptchoice").val(JSON.stringify(selectedDepts));
        //     // console.log(JSON.stringify(selectedDeptNames));
            
        //     return false;
        // }else{
        //     alert('Please choose at least 3 departments!');
        // }        
    });
    window.adToDeptList = function(deptId,deptName){
        if(jQuery.inArray(deptId,selectedDepts) !== -1){ // if item is already selected, remove it because user deselected it.
            selectedDepts.splice($.inArray(deptId,selectedDepts),1);
            selectedDeptNames.splice($.inArray(deptName,selectedDeptNames),1);
        }else{//going to add the item to the list.
            if (selectedDepts.length && selectedDepts.length >= 3) {
                alert('Select only 3 departments');
                $('#chkBox_'+deptId).prop({'checked':false});
                return false;
            }
            selectedDepts.push(deptId);
            selectedDeptNames.push(deptName);
        }

        console.log(selectedDeptNames);
        console.log(selectedDepts);
        dn = "";
        if (selectedDeptNames.length) {
            c = 1;
            $.each(selectedDeptNames,function(){
                console.log(this);
                dn += "<br>Choice "+c+" : "+ this;
                c++;
            });
            $('#deptChoiceContainer').html(dn);
        }
    }
</script>
