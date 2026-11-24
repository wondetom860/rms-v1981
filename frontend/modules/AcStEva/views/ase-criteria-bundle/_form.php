<?php

use yii\helpers\Html;
// use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseCriteriaBundle */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ase-criteria-bundle-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="hidden">
        <?= $form->field($model, 'eval_type_relId')->textInput(['readOnly' => true]) ?>
        <?= $form->field($model, 'criteria_IDS')->textInput() ?>
        <?= $form->field($model, 'employment_types')->textInput() ?>
    </div>

    <div class="row">
        <div class="col-md-12">
            <div class="row" style="background-color: #FED;">
                <div class="col-md-3">
                    <?= $form->field($model, 'evaluator_role')->dropdownList($model->getRoles(),['prompt' => 'Select Role']) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'candidate_staff_role')->dropdownList($model->getRoles(),['prompt' => 'Select Role']) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'average_point')->textInput(['value' => 5]) ?>
                </div>
                <div class="col-md-3">
                    <?= $form->field($model, 'average_percentage')->textInput() ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12"  style="max-height: 410px; overflow-x: auto; border: 1px solid black;">
                    <?= $this->render('../ase-criteria-description/__report/evaluation-criteria-all',['model' => $model]) ?></div>
                <div class="col-md-9">
                    Selected Criteria : <span style="font-style: italic; font-weight: bold; font-size: 7pt;" id="choices">-</span>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12" style="max-height: 310px; overflow-x: auto; border: 1px solid black;">
                    <?= $this->render('__report/employment-types-all',['model' => $model]) ?></div>
                <div class="col-md-9">
                    Selected Employment Types : <span style="font-style: italic; font-weight: bold; font-size: 7pt;" id="choices2">-</span>
                </div>
            </div>
            <div class="row"><br>
                <div class="col-md-7">
                    <?= $form->field($model, 'evaluate_per_course')->checkbox() ?>
                </div>
                <div class="col-md-5"><br>
                    <div class="form-group bg-warning" style="background-color: #78F;">
                        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success btn-xs pull-right']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<!-- <?php //$model->criteria_IDS ? json_decode($model->criteria_IDS) : [] ?> -->

<script type="text/javascript">
    __selected_criteria = [];
    __selected_empTypes = [];
    document.addEventListener("DOMContentLoaded", function(){
        ex = $('#asecriteriabundle-criteria_ids').val();
        et = $('#asecriteriabundle-employment_types').val();
        //  ? JSON.parse(ex) : []
        try{
            console.log(JSON.parse(ex));
            __selected_criteria = JSON.parse(ex);
            $("#choices").text(JSON.stringify(findCriteriaCode(__selected_criteria)));            
        }catch(err){
            console.log(err);
        }
        // 
        try{
            console.log(JSON.parse(et));
            __selected_empTypes = JSON.parse(et);
            $("#choices2").text(JSON.stringify(findEmpTypeCode(__selected_empTypes)));
        }catch(err){
            console.log(err);
        } 
    });
    
    window.addToEmpTypeList = (empTypeId) => {
        r = 0;
        if(jQuery.inArray(empTypeId,__selected_empTypes) !== -1){ 
            // if item is already selected, remove it because user deselected it.
            __selected_empTypes.splice($.inArray(empTypeId,__selected_empTypes),1);
            r= 0;
        }else{//going to add the item to the list.
            __selected_empTypes.push(empTypeId);
            r= 1;
        }
        $('#asecriteriabundle-employment_types').val(JSON.stringify(__selected_empTypes));
        $('#cat__'+empTypeId).prop({'checked':(r == 1)});
        $("#choices2").text(JSON.stringify(findEmpTypeCode(__selected_empTypes)));
    }

    window.addToCriteriaList = (criteriaId) => {
        r = 0;
        if(jQuery.inArray(criteriaId,__selected_criteria) !== -1){ 
            // if item is already selected, remove it because user deselected it.
            __selected_criteria.splice($.inArray(criteriaId,__selected_criteria),1);
            r= 0;
        }else{//going to add the item to the list.
            __selected_criteria.push(criteriaId);
            r= 1;
        }
        $('#asecriteriabundle-criteria_ids').val(JSON.stringify(__selected_criteria));
        $('#cat__'+criteriaId).prop({'checked':(r == 1)});
        $("#choices").text(JSON.stringify(findCriteriaCode(__selected_criteria)));
    }

    window.toggleDisplay = (id) => {
        $('#'+id).toggle('display');
    }

    window.findCriteriaCode = (__selected_criteria) => {
        code = [];
        $.each(__selected_criteria,(index,criteria) => { 
            code.push($("#cat__"+criteria).data('code')); 
            $('#cat__'+criteria).prop({'checked':true}); 
        });
        return code;
    }

    window.findEmpTypeCode = (__selected_empTypes) => {
        code = [];
        $.each(__selected_empTypes,(index,criteria) => { 
            code.push($("#eat_"+criteria).data('type')); 
            $('#eat_'+criteria).prop({'checked':true}); 
        });
        return code;
    }
</script>
