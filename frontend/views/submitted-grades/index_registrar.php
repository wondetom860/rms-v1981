<?php

use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;


$this->title = 'Submited Grades';

?>

<h2>Submitted Grades List - <b>For Registrar</b></h2>

<div class="container-fluid">
	<div class="row">
		<div class="panel panel-primary">
			<div class="panel-heading">
				<div class="panel-heading">
               	<?php $form = ActiveForm::begin(); ?>
                   <div class="row">
		                <div class="col-md-2">

		                  <?= $form->field($model, 'admission')->dropdownList(
		                      ArrayHelper::map(\backend\models\Admission::find()->all(),'id','admission_name'),
		                      [
		                      'prompt'=>'Select admission.',
		                      'required'=>TRUE,
		                  ])->label('Admission') ?>
		                  
		                </div>
	                   	<div class="col-md-2">
	                    	<?= $form->field($model, 'program')->dropdownList(
		                        ArrayHelper::map(\backend\models\Program::find()->all(),'id','program_name'),
			                        [
			                        'prompt'=>'Select Program',
			                        'required'=>TRUE,
			                            'onChange'=>' 
			                                $.post( "/program/clslvllists",
			                                    {
			                                        progId:$(this).val()
			                                    }, 
			                                  function( data ) {
			                                      //console.log("Returned: "+data);
			                                      $("#studentsemesterstatushistory-sssh_clslvl_relid" ).html( data );
			                                      loadGroup();   
			                                });',

		                    	])->label('Program') 
	                    	?>

	                   	</div>
              
                      	<div class="col-md-2">
                         <?= $form->field($model, 'sssh_clslvl_relId')->dropdownList(
                          ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
                                [
                                     'prompt'=>'Select Class Level',

                                      'onChange'=>'
                                      	loadGroup();
                                      ',
                            ])->label('ClassLevel') ?>
                      	</div>


                      	<div class="col-md-2">

	                       <?= $form->field($model, 'sssh_acadYear_relId')->dropdownList(
	                            ArrayHelper::map(\backend\models\SchoolYear::find()->all(),'id','sch_year_desc'),
	                            [
	                            'prompt'=>'Select academic year.',
	                            'onChange'=>'
	                            	loadGroup();
	                            ',

	                            
	                        ])->label('Academic Year') ?>
                       	</div>

                       	<div class="col-md-2">
	                        <?= $form->field($model, 'sssh_sem_relId')->dropdownList(
	                            ArrayHelper::map(\backend\models\Semester::find()->all(),'id','semester_name'),
	                            [
	                            'prompt'=>'Select semester.',
	                            'onChange'=>'
	                              loadGroup();
	                            ',
	                        ])->label('Semester') ?>
                       	</div>

                       	<div class="col-md-2">
	                        <?= 
	                        $form->field($model, 'group')->dropdownList(
	                          ArrayHelper::map(\backend\models\WGroup::find()->all(),'id','group_name'),
	                            [
	                            'prompt'=>'Select group.',
	                            'required'=>TRUE,
	                            'onChange'=>'loadInsts()',
	                        ])->label('Group') 
	                        ?>

                       	</div>

                      	</div>
	                    <div class="row">
	                       <div class="col-md-9">                  
	                        <button class="btn btn-primary btn-sm" id="loadStudents">Load Submitted Grades</button>
	                       </div>
		                </div>
	                   	<?php ActiveForm::end(); ?>
                   </div>
			</div>
			<div class="panel-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-6">
							<h5 class="bg-success">Instructors</h5>
							<div class="container-fluid" id="groupCourseInstContainer">
								
							</div>
						</div>
						<div class="col-md-6">
							<h5 class="bg-success">Submitted Grades</h5>
							<div class="container-fluid" id="groupCourseGradeContainer">
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<!-- groupCourseInstContainer -->
<!-- groupCourseGradeContainer -->

<?php

$this->registerJs(
	"
	window.showGrades = function(coffId,gsetId,courseId){
		$('#groupCourseGradeContainer').html('Loading Grades of the group...');
		$.post('/submitted-grades/load-submitted-grades',{coffId:coffId,gsetId:gsetId,courseId:courseId}).done(function(data){
			$('#groupCourseGradeContainer').html(data);

		});
	}

	window.loadGroup = function(){
		$.post('/w-group-setup/get-group-lists-peryear',
	        {
	          acadYearId:$('#studentsemesterstatushistory-sssh_acadyear_relid').val(),
	          semId:$('#studentsemesterstatushistory-sssh_sem_relid').val(),
	          admId:$('#studentsemesterstatushistory-admission').val(),
	          progId:$('#studentsemesterstatushistory-program').val(),
	          clslvlId:$('#studentsemesterstatushistory-sssh_clslvl_relid').val(),
	          //deptId:$('#studentsemesterstatushistory-department').val(),
	          specId:$('#studentsemesterstatushistory-spec').val()
	        }, 
	        function( data ) {
	            $('select#studentsemesterstatushistory-group').html( data ).trigger('change');
	        }); 
	}

	window.loadInsts = function(){
		groupId = $('#studentsemesterstatushistory-group').val();
		acadYearId = $('#studentsemesterstatushistory-sssh_acadyear_relid').val();
	    semId = $('#studentsemesterstatushistory-sssh_sem_relid').val();
	    admId = $('#studentsemesterstatushistory-admission').val();
	    progId = $('#studentsemesterstatushistory-program').val();
	    clslvlId = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();
	    gsetId = $('#studentsemesterstatushistory-group').val(); 
		if(groupId == '' || groupId == ' '|| typeof groupId == 'undefined'){
			$('#groupCourseInstContainer').html('Group Not Selected');
		}else{
			$('#groupCourseInstContainer').html('Loading Instructors and their held courses for the group.');
			$.post('/submitted-grades/load-group-instructors',
				{
					groupId:groupId
				}
			).done(function(data){
				$('#groupCourseInstContainer').html(data);
				// console.log(data);
			})
		}
	}


	$('#loadStudents').click(function(e){
		e.preventDefault();
		loadInsts();
	})
	",
	$this::POS_READY);

?>



