<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/* @var $model frontend\modules\DepartmentSelection\models\SelectionSettingEntry */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="selection-setting-entry-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'acadYear_relId')->dropdownlist(ArrayHelper::map(\backend\models\SchoolYear::find()->orderBy(['sch_year_desc' => SORT_DESC])->all(),'id','sch_year_desc'),['prompt' => 'Select Acsdemic Year']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'sem_relId')->dropdownlist(ArrayHelper::map(\backend\models\Semester::find()->all(),'id','semester_name'),['prompt' => 'Select Acsdemic Year']) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'clslvl_relId')->dropdownlist(ArrayHelper::map(\backend\models\ClassLevel::find()->all(),'id',function($model){
                return $model->clslvl_num." => ".$model->clslvlProgramRel->program_name;
            }),['prompt' => 'Select Acsdemic Year']) ?>
        </div>
    </div><hr>
    <div class="row">
        <div class="col-md-4">
            <div class="col-md-12">
                <?= $form->field($model, 'max_count_dept')->textInput(['placeholder' => 3]) ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($model, 'date_from')->widget(DatePicker::className(),[
                        'pluginOptions'=>[
                            'autoClose'=>true,
                            'format'=>'yyyy-m-dd',
                        ],
                ]) ?>
            </div>
            <div class="col-md-12">
                <?= $form->field($model, 'date_to')->widget(DatePicker::className(),[
                    'pluginOptions'=>[
                        'autoClose'=>true,
                        'format'=>'yyyy-m-dd',
                    ],
            ]) ?>
            </div>
            <div class="col-md-12 pull-right">
                <div class="form-group pull-right"><br>
                    <?= Html::submitButton(Yii::t('decris', 'Save'), ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>
        <div class="col-md-8" style="font-size: 8pt;">
            <div class="hidden">
                <?= $form->field($model, 'depts_to_choose_from')->textInput(['readonly' => true]) ?>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <h4>Available Departments</h4>
                    <?= $this->render('dept_list') ?>
                </div>
                <div class="col-md-6">
                    <h4>Departments to choose from:</h4>
                    <?= $this->render('dept_list_hidden',['model' => $model]) ?>
                </div>
            </div>
        </div>
    </div>

    <?php 

    $this->registerJs(
        "
        selectedDepts = [];
        window.addToSelected = (deptId) =>{
            if(jQuery.inArray(deptId,selectedDepts) !== -1){ // if item is already selected, remove it because user deselected it.
                selectedDepts.splice($.inArray(deptId,selectedDepts),1);
            }else{//going to add the item to the list.
                selectedDepts.push(deptId);
            }
            $('#selectionsettingentry-depts_to_choose_from').val(JSON.stringify(selectedDepts));
            displayList();
        }

        window.displayList = ()=>{
            // console.log((selectedDepts.length) > 0);
            $('.tr_hidden').addClass('hidden');
            if ((selectedDepts.length) > 0) { $('#dept_list_table_hidden').removeClass('hidden'); }
            else{ $('#dept_list_table_hidden').addClass('hidden'); }
            $.each(selectedDepts,function(key,value){
                $('#'+value+'_tr_hidden').removeClass('hidden');
                $('#'+value+'_order_count').html(Number(key+1));
            });
        }
        ",
        $this::POS_READY);

    ?>

    <?php ActiveForm::end(); ?>

</div>
