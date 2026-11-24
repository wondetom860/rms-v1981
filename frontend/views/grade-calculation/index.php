<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
?>


<?php $form = ActiveForm::begin();?>
<div class="container-fluid">
	<div class="row">
		<h3 class="bg-info" style="text-align: center; font-style: italic;">Generate Grade Report</h3>
		<div class="col-md-4">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4>Select Student</h4>
				</div>
				<div class="panel-body">
					<div class="col-md-12">
		                
                        <!-- <?= $form->field($model, 'sgh_acadYear_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\SchoolYear::find()->all(), 'id', 'sch_year_desc'),
                            [
                                'prompt'=>'Select Academic Year',
                                'onChange'=>'coff2Load()',
                            ]
                        ) ?>
-->
                        <?= $form->field($model, 'sgh_adm_relId')->radioList(
                                ArrayHelper::map(
                                        backend\models\Admission::find()->all(), 'id', 'admission_name'
                                ),[
                                    'onChange'=>'
                                        // 
                                    '

                            ])->label('Admission')?>

                        <?= $form->field($model, 'sgh_prog_relId')->radioList(
                                ArrayHelper::map(
                                        backend\models\Program::find()->all(), 'id', 'program_name'
                                ),[
                                    'onChange'=>'

                                        $progIDD = $("#studentgrouphistory-sgh_prog_relid :checked").val();
                                        $.post( "'.Yii::$app->urlManager->createUrl('class-level/classyearlists').'",
                                            {
                                                progId:$progIDD
                                            }, 
                                            function( data ) {
                                                // console.log("Class Year: Returned: "+data);
                                              $("#studentgrouphistory-sgh_classlevel").html( data );
                                              });
                                    '
                            ])->label('Program')?>

                        <?= $form->field($model, 'sgh_dept_relId')->dropDownList(
                                ArrayHelper::map(
                                        backend\models\Department::find()->all(), 'id', 'dept_name'),
                                        ['prompt'=>'Select Department',
                                        'onChange'=>'
                                                $.post( "'.Yii::$app->urlManager->createUrl('specialization/listspec').'",
                                            {
                                            admId:$("#studentgrouphistory-sgh_adm_relid :checked").val(),
                                            deptId:$(this).val(),
                                            progId:$("#studentgrouphistory-sgh_prog_relid :checked").val(),
                                            clslvl:$("#studentgrouphistory-sgh_classlevel").val()
                                            }, 
                                            function( data ) {
                                            // console.log("Returned: SPECS:  "+data);
                                          $( "select#studentgrouphistory-sgh_spec_relid" ).html( data );
                                        });

                                        ',
                                    ]
                                )->label('Department')?>

                        <?= $form->field($model, 'sgh_classlevel')->dropdownList(
                            ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
                            [
                                'prompt'=>'Select Class Year',
                                'onChange'=>'//coffLoad()',
                            ]
                        ) ?>

                        <?= $form->field($model, 'sgh_spec_relId')->dropDownList(
                                ArrayHelper::map(
                                        backend\models\Specialization::find()->all(), 'id', 'spec_name'
                                ),['prompt'=>'Select Specialization'])->label('Specialization')?>


                        <?= $form->field($model, 'sgh_semester_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\Semester::find()->all(), 'id', 'semester_name'),
                            [
                                'prompt'=>'Select Semester',
                                'onChange'=>'//coff2Load()',
                            ]
                        ) ?>
                        <?= $form->field($model, 'sgh_acadYear_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\SchoolYear::find()->all(), 'id', 'sch_year_desc'),
                            [
                                'prompt'=>'Select Academic Year',
                                'onChange'=>'//coff2Load()',
                            ]
                        ) ?>     
				</div>
				<div class="panel-footer">
					<button class="btn btn-sm" id="loadStudentList">
                        Load Student List
                    </button> 
				</div>
			</div>
		</div>
		</div>
		<div class="col-md-8">
			<div class="panel panel-primary">
				<div class="panel-heading">
					<h4>Semester Grade status of selected student.</h4>
				</div>
				<div class="panel-body">
					<div class="container-fluid" id="stdListContainer" style=" height: 300px;overflow-y: auto; border: 1px solid brown;">
						Loading...
					</div>
					<div class="container-fluid" id="grprtContainer">
						Loading...
					</div>
				</div>
				<div class="panel-footer">
					
				</div>
			</div>
		</div>
	</div>
</div>

<!-- stdListContainer -->
<!-- loadStudentList - btn to click -->
<!-- grprtContainer -->

<?php
$this->registerJs(
    "
    window.printCont = function(el){
        // console.log(el);
        var restorePage = $('body').html();
        var printContent = $('#print_grpoint').html();
        $('body').html(printContent);
        window.print();
        $('body').html(restorePage);
    }
    window.generate = function(stdId){
    	$('#grprtContainer').html('Generating grade for selected student');
    	$.post('/grade-calculation/calculategrade',{stdId:stdId},function(data){
    		$('#grprtContainer').html(data);
    		})
    }
    $('#loadStudentList').click(function(e){
        e.preventDefault();

        admId = $('#studentgrouphistory-sgh_adm_relid :checked').val();
        progId = $('#studentgrouphistory-sgh_prog_relid :checked').val();
        deptId = $('#studentgrouphistory-sgh_dept_relid').val();
        clslvlId = $('#studentgrouphistory-sgh_classlevel').val();
        specId = $('#studentgrouphistory-sgh_spec_relid').val();
        acadYearId = $('#studentgrouphistory-sgh_acadyear_relid').val();
        semId = $('#studentgrouphistory-sgh_semester_relid').val();
        
        // $('#stdListContainer').html(admId,progId,deptId,clslvlId,specId, acadYearId,semId);
        // console.log(admId,progId,deptId,clslvlId,specId, acadYearId,semId);
        $.post('/student/getlist',{

        	admId:admId,
        	progId:progId,
        	deptId:deptId,
        	clslvlId:clslvlId,
        	specId:specId=='undefined'?0:specId,
        	acadYearId:acadYearId,
        	semId:semId

        	},function(data){
        	// console.log(data);
        	$('#stdListContainer').html(data);
        	});
        });
    ",
    $this::POS_READY
);
?>