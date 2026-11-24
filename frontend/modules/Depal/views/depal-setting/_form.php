<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Depal\models\DepalSetting */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="depal-setting-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'section_relId')->dropdownList(ArrayHelper::map($model->getActiveAcademicSections(),'id','clslvl',['prompt' => 'select Section'])) ?>
            <?= $form->field($model, 'depts_count')->textInput() ?>

            <?= $form->field($model, 'startdate')->widget(DatePicker::className(),[
                'pluginOptions'=>[
                    'autoClose'=>true,
                    'format'=>'dd-m-yyyy',
                ],
            ]) ?>

            <?= $form->field($model, 'enddate')->widget(DatePicker::className(),[
                'pluginOptions'=>[
                    'autoClose'=>true,
                    'format'=>'dd-m-yyyy',
                ],
            ]) ?>
        </div>
        <div class="col-md-6">
            <div class="hidden">
                <?= $form->field($model, 'category_IDS')->textInput() ?>
                <?= $form->field($model, 'elligible_depts')->textInput() ?>   
            </div>
            <div class="container-fluid">
                <h4 style="text-align: center;">Applicable allotment criteria catories</h4>
                <div class="container-fluid" style="font-size: 0.9em">
                    <?= $this->render('__supply/criteria-category',[])  ?>
                </div>
            </div>
            <div class="container-fluid">
                <h4 style="text-align: center;">Eligible Departments for selection</h4>
                <div class="container-fluid" style="font-size: 0.9em">
                    <?= $this->render('__supply/academic-depts',[])  ?>
                </div>
            </div>
        </div>
        
    </div>

    <div class="form-group" style="background-color: #FD9;">
        <center><?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?></center>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
    deptList = [];
    categoryList = [];
    window.addToDeptList = (deptId) => {
        r = 0;
        if(jQuery.inArray(deptId,deptList) !== -1){ 
            // if item is already selected, remove it because user deselected it.
            deptList.splice($.inArray(deptId,deptList),1);
            r= 0;
        }else{//going to add the item to the list.
            deptList.push(deptId);
            r= 1;
        }
        $('#depalsetting-elligible_depts').val(JSON.stringify(deptList));
        $('#dept_'+deptId).prop({'checked':(r == 1)});
    }
    window.addToCategoryList = (categroyId) => {
        r = 0;
        if(jQuery.inArray(categroyId,categoryList) !== -1){ 
            // if item is already selected, remove it because user deselected it.
            categoryList.splice($.inArray(categroyId,categoryList),1);
            r= 0;
        }else{//going to add the item to the list.
            categoryList.push(categroyId);
            r= 1;
        }
        $('#depalsetting-category_ids').val(JSON.stringify(categoryList));
        $('#cat_'+categroyId).prop({'checked':(r == 1)});
    }
</script>