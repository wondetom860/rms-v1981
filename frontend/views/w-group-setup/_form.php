<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\GroupSetup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="group-setup-form">

    <?php $form = ActiveForm::begin($model->id ? ['action' => '/w-group-setup/update?id='.$model->id]:['action' => '/w-group-setup/create']); ?>
    <div class="container-fluid sm">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        New Group setup wizard
                    </div>
                    <div class="panel-body">
                        <div class="container-fluid">
                            <div class="row">
                                <div class="col-md-3">
                                    <?= $form->field($model, 'gs_adm_relId')->dropdownList(
                                        ArrayHelper::map(\backend\models\Admission::find()->all(), 'id', 'admission_name'),
                                        [
                                            'prompt'=>'Select Admission',
                                        ]
                                    ) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->field($model, 'gs_prog_relId')->dropdownList(
                                        ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
                                        [
                                            'prompt'=>'Select Program',
                                            'onChange'=>' $.post( "'.Yii::$app->urlManager->createUrl('program/clslvllists').'",
                                                {
                                                    progId:$(this).val()
                                                }, 
                                                function( data ) {
                                                //console.log("Returned: "+data);
                                              $( "select#wgroupsetup-gs_clslvl_relid" ).html( data );
                                            });
                                            // coffLoad();',
                                        ]
                                    ) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->field($model, 'gs_dept_relId')->dropdownList(
                                        ArrayHelper::map(\backend\models\Department::find()->all(), 'id', 'dept_name'),
                                        [
                                            'prompt'=>'Select Department',
                                            'onChange'=>' 
                                                $.post( "'.Yii::$app->urlManager->createUrl('specialization/listspec-cp').'",
                                                    {
                                                        deptId:$(this).val(),
                                                        admId:$("#wgroupsetup-gs_adm_relid").val(),
                                                        progId:$("#wgroupsetup-gs_prog_relid").val()   
                                                    }, 
                                                    function( data ) {
                                                    //console.log("Returned: "+data);
                                                  $( "#wgroupsetup-gs_spec_relid").html( data );
                                                });
                                               loadFreeGroups();
                                            ',
                                        ]
                                    ) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->field($model, 'gs_clslvl_relId')->dropdownList(
                                        ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
                                        [
                                            'prompt'=>'Select Class Level',
                                            'onChange'=>'
                                                loadFreeGroups();
                                            ',
                                        ]
                                    ) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-3">
                                    <?= $form->field($model, 'gs_spec_relId')->dropdownList(
                                        ArrayHelper::map(\backend\models\Specialization::find()->all(), 'id', 'spec_name'),
                                        [
                                            'prompt'=>'Select Specialization',
                                        ]
                                    ) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->field($model, 'gs_acadYear_relId')->dropdownList(
                                        ArrayHelper::map(\backend\models\SchoolYear::find()->all(), 'id', 'sch_year_desc'),
                                        [
                                            'prompt'=>'Select Acad. Yr.',
                                            
                                        ]
                                    ) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->field($model, 'gs_sem_relId')->dropdownList(
                                        ArrayHelper::map(\backend\models\Semester::find()->all(), 'id', 'semester_name'),
                                        [
                                            'prompt'=>'Select Semester',
                                            'onChange'=>'
                                                $("#loadCocatDiv").show();
                                            ',
                                        ]
                                    ) ?>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->field($model, 'gs_group_relId')->dropdownList(
                                        ArrayHelper::map(\backend\models\WGroup::find()->all(), 'id', 'group_name'),
                                        [
                                            'prompt'=>'Select Group',
                                            'onChange'=>'
                                                // $("#loadCocatDiv").show();
                                            ',
                                        ]
                                    ) ?>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-md-4">
                                    <?= $form->field($model, 'gs_size')->textInput(['placeholder'=>'Default is 35.']) ?>
                                </div>
                                <div class="col-md-4">
                                    <?= $form->field($model, 'remark')->textarea(['rows' => 1]) ?>
                                </div>
                                <div class="hidden">
                                    <?= $form->field($model, 'gs_active_prog_relId')->textarea(['rows' => 1]) ?>
                                </div>
                                <div class="col-md-4" style="background-color: white;"><br>
                                    <div class="form-group pull-right" style="margin-top: 5px; ">
                                        <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Save') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-xs btn-success' : 'btn btn-xs btn-primary'])?>
                                    </div>
                                </div>
                            </div>
                        </div>
                   </div>
                </div>
            </div>
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <i class="bg-secondary"><?php
                        echo 'Group setups made for this semester of Year: '.Yii::$app->params['C']['Y'] ?></i>
                    </div>
                    <div class="panel-body">
                        <div class="container-fluid" id="groupSetupHistoryContainer">
                            
                        </div>
                    </div>
                    <div class="panel-footer">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>
    

    <?php ActiveForm::end(); ?>

</div>
<!-- c -->

<?php
$this->registerJs(
    "

    window.loadFreeGroups = function(){
        // 
        admId = $('#wgroupsetup-gs_adm_relid').val();
        clslvlId = $('#wgroupsetup-gs_clslvl_relid').val();
        progId = $('#wgroupsetup-gs_prog_relid').val();
        acadYearId = $('#wgroupsetup-gs_acadyear_relid').val();
        semId = $('#wgroupsetup-gs_sem_relid').val();
        console.log(admId,progId, clslvlId);
        $.post('/w-group-setup/get-free-group-list',
        {
            admId:admId,
            clslvlId:clslvlId,
            progId:progId,
            acadYearId:acadYearId,
            semId:semId
            },
        function(data){
                $('#wgroupsetup-gs_group_relid').html(data);
            });
    }
    window.loadGroup = function(){
        $.post('/w-group-setup/getlist',{},function(data){
                $('#groupSetupHistoryContainer').html(data);
            });
    }
    loadGroup();
    loadFreeGroups();
    ",
    $this::POS_READY
);
?>