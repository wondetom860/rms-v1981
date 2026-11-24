<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\GroupSetup */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Mark Internship Group';
?>

<div class="group-setup-form">

    <?php $form = ActiveForm::begin(['class'=>'sm']); ?>
    <div class="container-fluid sm">
        <div class="row" style="font-size: 9pt;">
            <div class="row">
                <div class="col-md-2">
                    <?= $form->field($model, 'gs_adm_relId')->dropdownList(
                        ArrayHelper::map(\backend\models\Admission::find()->all(), 'id', 'admission_name'),
                        [
                            'prompt'=>'Select Admission',
                        ]
                    ) ?>
                </div>
                <div class="col-md-2">
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
                <div class="col-md-2">
                    <?= $form->field($model, 'gs_clslvl_relId')->dropdownList(
                        ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
                        [
                            'prompt'=>'Select Class Level',
                            'onChange'=>'
                                // loadFreeGroups();
                            ',
                        ]
                    ) ?>
                </div>

                <div class="col-md-2">
                    <?= $form->field($model, 'gs_acadYear_relId')->dropdownList(
                        ArrayHelper::map(\backend\models\SchoolYear::find()->all(), 'id', 'sch_year_desc'),
                        [
                            'prompt'=>'Select Acad. Yr.',
                            
                        ]
                    ) ?>
                </div>
                <div class="col-md-2">
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
                <div class="col-md-1"><br>
                    <button class="btn btn-primary" onclick="getGsets();return false;">Get Record</button>
                </div>
            </div>
            <div class="col-md-7">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4>Group setups</h4>
                    </div>
                    <div class="panel-body">
                        <div class="container-fluid" id="groupSetupHistoryContainer">
                            
                        </div>
                    </div>
                    <div class="panel-footer">
                        
                    </div>
                </div>
            </div>
            <div class="col-md-5">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        <h4>Marked Groups</h4>
                    </div>
                    <div class="panel-body">
                        <div class="container-fluid" id="marked_groups">
                            
                        </div><hr>
                        <button class="btn btn-xs btn-warning pull-right" onclick="saveRecord();return false;">Save</button>
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

    internshipGsets = [];

    window.unmarkInternship = (gsetId)=>{
        if(confirm('Are you suret to mark selected groups as INTERSHIP GROUP?')){
            $.post('unmark-internship',{gsetId:gsetId}).done((result) => {
                if(result > 0){
                    getGsets();
                }else{
                    $('#marked_groups').html(result);
                }
            });
        }
    }
    window.saveRecord = () => {
        if(internshipGsets.length){
            if(confirm('Are you suret to mark selected groups as INTERSHIP GROUP?')){
                $.post('mark-gset-last',{internshipGsets:JSON.stringify(internshipGsets)}).done((result) => {
                    if(result > 0){
                        getGsets();
                    }else{
                        $('#marked_groups').html(result);
                    }
                });
            }
        }
    }
    window.getGsets = function(){
        // 
        admId = $('#wgroupsetup-gs_adm_relid').val();
        clslvlId = $('#wgroupsetup-gs_clslvl_relid').val();
        progId = $('#wgroupsetup-gs_prog_relid').val();
        acadYearId = $('#wgroupsetup-gs_acadyear_relid').val();
        semId = $('#wgroupsetup-gs_sem_relid').val();
        $('#groupSetupHistoryContainer').html('Getting group setups...');
        $('#marked_groups').html('');
        internshipGsets = [];
        $.post('get-gsets',
        {
            admId:admId,
            clslvlId:clslvlId,
            progId:progId,
            acadYearId:acadYearId,
            semId:semId
            },
        function(data){
                $('#groupSetupHistoryContainer').html(data);
            });
    }
    window.addToList = function(gsetId){
        if(jQuery.inArray(gsetId,internshipGsets) !== -1){ 
            internshipGsets.splice($.inArray(gsetId,internshipGsets),1);
        }else{//going to add the item to the list.
            internshipGsets.push(gsetId);
        }
        $('#marked_groups').html('Marked '+ Number(internshipGsets.length) + ' groups as Interneship group.');
    }

    ",
    $this::POS_READY
);
?>