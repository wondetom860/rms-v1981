<?php
/** @var yii\web\View $this */

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$this->title = "Academic Defficiency Students";
?>

<div class="student-semester-status-history-index">
	   <div class="container-fluid">
	       <div class="row">
	           <div class="panel panel-primary">
	               <div class="panel-heading">
	                   <div class="row">
	                   	<?php $form = ActiveForm::begin(); ?>
	                     <div class="col-md-2">

	                      <?= $form->field($model, 'admission')->dropdownList(
	                          ArrayHelper::map(\backend\models\Admission::find()->all(),'id','admission_name'),
	                          [
	                          'prompt'=>'Select admission.',
	                      ])->label('Admission') ?>
	                      
	                     </div>
	                       <div class="col-md-2">
	                        <?= $form->field($model, 'program')->dropdownList(
	                            ArrayHelper::map(\backend\models\Program::find()->all(),'id','program_name'),
	                            [
	                            'prompt'=>'Select Program',
                                'onChange'=>' 
                                    $.post( "/program/clslvllists",
                                        {
                                            progId:$(this).val()
                                        }, 
                                      function( data ) {
                                          //console.log("Returned: "+data);
                                          $("#studentsemesterstatushistory-sssh_clslvl_relid" ).html( data );
                                          LoatStatus();       
                                    }); ',


	                        ])->label('Program') ?>

	                       </div>
	              
	                      <div class="col-md-2">
	                         <?= $form->field($model, 'sssh_clslvl_relId')->dropdownList(
	                          ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
	                                [
	                                     'prompt'=>'Select Class Level',

	                                      'onChange'=>'LoatStatus()',
	                            ])->label('ClassLevel') ?>
	                      </div>


	                      <div class="col-md-2">

	                       <?= $form->field($model, 'sssh_acadYear_relId')->dropdownList(
	                            ArrayHelper::map(\backend\models\SchoolYear::find()->all(),'id','sch_year_desc'),
	                            [
	                            'prompt'=>'Select academic year.',
	                            'onChange'=>'LoatStatus()',

	                            
	                        ])->label('Academic Year') ?>
	                       </div>

	                       <div class="col-md-2">
	                        <?= $form->field($model, 'sssh_sem_relId')->dropdownList(
	                            ArrayHelper::map(\backend\models\Semester::find()->all(),'id','semester_name'),
	                            [
	                            'prompt'=>'Select semester.',
	                            'onChange'=>'LoatStatus()',
	                        ])->label('Semester') ?>
	                       </div>
	                       <?php ActiveForm::end(); ?>
	                       <div class="col-md-2">  <br>                
	                        	<button class="btn btn-primary btn-sm pull-right" onclick="LoatStatus();return false;">Load Students</button>
	                       </div>
	                   </div>
	               </div>
	               <div class="panel-body">
	                   <div class="col-md-12 col-lg-12 colsm-12">
	                      <hr>
	                      <i id="printIcon" style="cursor: pointer; display: none; float: right; color: blue;" title="Print" onclick="printCont('selectionFormContainer')">Print<span class="glyphicon glyphicon-print"></span></i>
	                       <div class="container-fluid table-responsive" id="studentSemesterStatusContainer" style="border: 1px dashed black;">
	                           Waiting for your selection...
	                       </div>
	                   </div>
	               </div>
	           </div>
	       </div>
	   </div>
	</div>

	<?php

	$this->registerJs(
		"
		window.LoatStatus = function(){
			acadYearId = $('#studentsemesterstatushistory-sssh_acadyear_relid').val();
		    semId = $('#studentsemesterstatushistory-sssh_sem_relid').val();
	      	admId = $('#studentsemesterstatushistory-admission').val();
	      	progId = $('#studentsemesterstatushistory-program').val();
	      	clslvlId = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();
	      	deptId = $('#studentsemesterstatushistory-dept').val(); 
	      	spec = $('#studentsemesterstatushistory-spec').val(); 

	      	$('#studentSemesterStatusContainer').html('Loading result...');

	      	$.post('/academic-defficiency/get-ad-student-list',
		    {
		        acadYearId:acadYearId,
		        semId:semId,
		        admId:admId,
		        progId:progId,
		        deptId:deptId,
		        clslvlId:clslvlId,
		        spec:spec,

		        }).done(function(data){
		          $('#studentSemesterStatusContainer').html(data);
		          $('#printIcon').show();
		    });
	    }


	    window.adClas = function(id){
	    	$('#row_'+id).toggleClass('bg-success');
	    }
		window.printCont = function(el){
        // console.log(el);

	      // Add the print css to the table. with ID = reportTable
	      //$('#reportTable').addClass('table table-bordered');
	      $('#reportTable'). css({
	        'background-color': \"yellowgreen\",
	        \"font-weight\": \"bold\",
	        'border':'1px solid black'
	        });

	        $('.non-print').hide();

	      $('#reportTable td,th').css({
	        \"font-weight\": \"normal\",
	        'borderRight':'1px solid black',
	        'borderBottom':'1px solid black'
	        });
	      $('#reportTable th').css({
	        \"font-weight\": \"bold\"
	        });

	      // $('#reportTable tbody tr').css({
	      //   'borderBottom':'1px solid black'
	      //   });
	      $('#studentSemesterStatusContainer').css({
	        'borderTop':'2px solid black',
	        'textAlign':'center'
	        });

	        var restorePage = $('body').html();
	        var printContent = $('#studentSemesterStatusContainer').html();
	        $('body').html(printContent);
	        window.print();
	        $('body').html(restorePage);
	        $('.non-print').show();
	    }
		",
		$this::POS_READY);
	?>