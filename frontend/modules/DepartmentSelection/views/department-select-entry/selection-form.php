<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

$dept_count = $model->settingRel->max_count_dept;

/** @var yii\web\View $this */
/* @var $model frontend\modules\DepartmentSelection\models\DepartmentSelectEntry */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="department-select-entry-form">

    <?php $form = ActiveForm::begin(['action' => '/DepartmentSelection/department-select-entry/create']); ?>

    <div class="panel panel-info"  style="font-size: 9pt;">
        <div class="panel-heading"><h3>Department Selection</h3></div>
        <div class="panel-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-6">
                        <h4>Available Departments</h4>
                        <?= $this->render('dept_list',['settingModel' => $model->settingRel]) ?>
                    </div>
                    <div class="col-md-6">
                        <h4>Selected Department(s)</h4>
                        <small>You have to Select at least <?= $dept_count ?> departments.</small><hr>
                        <span class="hidden" id="max_count_dept"><?= $dept_count ?></span>
                        <div class="hidden"><?= $form->field($model, 'dept_choice')->textarea(['rows' => 1,'readonly' => true]) ?></div>
                        <div class="hidden"><?= $form->field($model, 'student_relId')->textarea(['rows' => 1,'readonly' => true]) ?></div>
                        <div class="hidden"><?= $form->field($model, 'setting_relId')->textarea(['rows' => 1,'readonly' => true]) ?></div>
                        <div class="container-fluid" id="selecteds_list_container">
                            <?= $this->render('dept_list_hidden') ?>
                        </div>
                    </div>
                </div><hr>
                <div class="row">
                    <div class="col-md-12"><br>
                        <div class="form-group pull-right">
                            <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <script type="text/javascript">
        window.saveForm = ()=>{
            if (confirm('Are you sure to save your department selection?')) {
                $.post('/DepartmentSelection/department-select-entry/save-selection',{selectedDepts:JSON.stringify(selectedDepts)}).done((resp) =>{ if (resp == 1) { window.location.href = window.location; }else{ alert(resp); } })
            }
        }
        window.addToSelected = (deptId,otpion)=>{
            // console.log(Number($("#max_count_dept").html()));
            if (otpion == 1 && (Number($("#max_count_dept").html()) <= selectedDepts.length)) { alert('Maximum Number of department to choose is ' + Number($("#max_count_dept").html())); return;}
            if(jQuery.inArray(deptId,selectedDepts) !== -1){ // if item is already selected, remove it because user deselected it.
                selectedDepts.splice($.inArray(deptId,selectedDepts),1);
            }else{//going to add the item to the list.
                selectedDepts.push(deptId);
            }

            $("#departmentselectentry-dept_choice").val(JSON.stringify(selectedDepts));

            displayList();
        }
        window.displayList = ()=>{
            // console.log((selectedDepts.length) > 0);
            $(".tr_hidden").addClass('hidden');
            if ((selectedDepts.length) > 0) { $("#dept_list_table_hidden").removeClass('hidden'); }
            else{ $("#dept_list_table_hidden").addClass('hidden'); }
            $.each(selectedDepts,function(key,value){
                // console.log(key+" => "+value);
                $("#"+value+"_tr_hidden").removeClass('hidden');
                $("#"+value+"_order_count").html(Number(key+1));
            });
        }
    </script>
    <?php ActiveForm::end(); ?>

</div>
