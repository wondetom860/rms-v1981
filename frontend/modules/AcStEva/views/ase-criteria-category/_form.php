<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseCriteriaCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ase-criteria-category-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="hidden">
        <?= $form->field($model, 'profess_list')->textInput(['maxlength' => true]) ?>
    </div>
    
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'category_name')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-8">
            <?= $this->render('__report/profession-types-all',['model' => $model]) ?>
        </div>
        <div class="col-md-4">
            Selected Employe Profession Types : <span style="font-style: italic; font-weight: bold; font-size: 7pt;" id="choices3">-</span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12"><br>
            <div class="form-group pull-right">
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    

    <?php ActiveForm::end(); ?>
</div>
<script type="text/javascript">
    __typeList = [];
    document.addEventListener("DOMContentLoaded", function(){
        pt = $('#asecriteriacategory-profess_list').val();
        //  ? JSON.parse(ex) : []
        try{
            console.log(JSON.parse(pt));
            __typeList = JSON.parse(pt);
            $("#choices3").text(JSON.stringify(__typeList)); 
        }catch(err){
            console.log(err);
        }
    });

    window.toggleDisplay = (id) => {
        $('#'+id).toggle('display');
    }
    window.addToProfTypeList = (type) => {
        r = 0;
        if(jQuery.inArray(type,__typeList) !== -1){ 
            // if item is already selected, remove it because user deselected it.
            __typeList.splice($.inArray(type,__typeList),1);
            r= 0;
        }else{//going to add the item to the list.
            __typeList.push(type);
            r= 1;
        }
        $('#asecriteriacategory-profess_list').val(JSON.stringify(__typeList));
        $('#cat__'+type).prop({'checked':(r == 1)});
        $("#choices3").text(JSON.stringify(__typeList));
    }
</script>