<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\GroupSetup */
/* @var $form yii\widgets\ActiveForm */

$this->title = "Internship elligiblity report";
?>

<div class="group-setup-form" style="font-size: 8pt;">

    <?php $form = ActiveForm::begin(['class'=>'sm']); ?>
    <div class="row" >
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
    </div><hr>
    <div class="row">
        <div class="col-md-4">
        	<h4>Group setups</h4>
        	<div class="container-fluid" id="groupSetupHistoryContainer"></div>
        </div>
        <div class="col-md-8 printable" style="background-color: #FFF; border: 1px dashed black;">
        	<i id="printIcon" style="cursor: pointer; display: none; float: right; color: blue;" title="Print" onclick="printCont()"><span class="glyphicon glyphicon-print"></span></i>
        	<div class="container-fluid" id="students_list"></div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
$this->registerJs(
    "

    window.printCont =()=>{
    	 myWindow = window.open(
          '/internship-eligibility/list-students-print', 
          'title=Internship eligibilty report', 
          'height=900,width=900');

        // myWindow.print();
        // return false;

    }
    window.listStudents = (gsetId)=>{
    	$('#students_list').html('Loading report.....');
    	$.post('/internship-eligibility/list-students',{gsetId:gsetId}).done((result) => {
    		$('#students_list').html(result);
    		$('#printIcon').show();
    	});
    }

    window.getGsets = function(){
        // 
        $('#printIcon').hide();
        admId = $('#wgroupsetup-gs_adm_relid').val();
        clslvlId = $('#wgroupsetup-gs_clslvl_relid').val();
        progId = $('#wgroupsetup-gs_prog_relid').val();
        acadYearId = $('#wgroupsetup-gs_acadyear_relid').val();
        semId = $('#wgroupsetup-gs_sem_relid').val();
        $('#groupSetupHistoryContainer').html('Getting group setups...');
        $('#marked_groups').html('');
        internshipGsets = [];
        $.post('/internship-eligibility/get-gsets',
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

    ",
    $this::POS_READY
);
?>