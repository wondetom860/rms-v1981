<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\Specialization */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="specialization-form">

    <?php $form = ActiveForm::begin();?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h3>Add new specialization</h3>
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'spec_adm_relId')->dropDownList(
                                    ArrayHelper::map(
                                            \backend\models\Admission::find()->all(), 'id', 'admission_name'
                                    ), ['prompt' => 'Select Admission','onchange'=>'loadSpecs()',]
                            ) ?>
            
                        <?= $form->field($model, 'spec_prog_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
                                    [
                                        'prompt'=>'Select Program',
                                        'onchange'=>'loadSpecs()',
                                    ]
                                ) ?>
                    
                        <?= $form->field($model, 'spec_dep_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\Department::find()->all(), 'id', 'dept_name'),
                                    [
                                        'prompt'=>'Select Department',
                                        'onchange'=>'loadSpecs()',
                                    ]
                                ) ?>
                    
                        <?= $form->field($model, 'spec_name')->textInput(['maxlength' => true]) ?>
                        <?= $form->field($model, 'spec_code')->textInput(['maxlength' => true]) ?>
                    
                       <?= $form->field($model, 'spec_desc')->textarea(['rows' => 1]) ?>
                    </div>
                    <div class="panel-footer">
                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-7">
                <div class="panel panel-warning">
                    <div class="panel-heading">
                        Avaliable Specializations. <span id="specCount"></span>
                    </div>
                    <div class="panel-body">
                        <div class="container-fluid" id="specContainer">
                            Loading.....
                        </div>
                    </div>
                    <div class="panel-footer">
                        <p class="bg-info"> Specializations in Depts.</p>
                    </div>
                </div>
            </div>
    </div>
</div>

    <?php ActiveForm::end(); ?>

</div>

<!-- specCount -->
<!-- specContainer -->

</div>
<?php
$this->registerJs(
    "
    window.loadSpecs = function(){
        admId = $('#specialization-spec_adm_relid').val();
        progId = $('#specialization-spec_prog_relid').val();
        deptId = $('#specialization-spec_dep_relid').val();
        $.post(
        '/specialization/listspecs2',
        {
            admId:admId,
            progId:progId,
            deptId:deptId
            },
        function(data){
            // console.log(data);
            $('#specContainer').html(data);
            });
    }
    loadSpecs();
    ",
    $this::POS_READY
);
?>
