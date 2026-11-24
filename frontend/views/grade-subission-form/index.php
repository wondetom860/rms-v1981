<?php
// ini_set("memory_limit", "1024M");
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\GradeScaleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decris', 'Grade Submission Form');
$this->params['breadcrumbs'][] = $this->title;
?>

<h3 style="text-decoration:underline; text-decoration-color: blue;"><?= $this->title ?> - <b>For Registrar</b></h3>

<div class="container-fluid">
	<div class="row">
		<div class="panel panel-default">
			<div class="panel-heading">
				<div class="panel-heading">
               	<?php $form = ActiveForm::begin(); ?>
                   <div class="row">
		                <div class="col-md-3">

		                  <?= $form->field($model, 'admission')->dropdownList(
		                      ArrayHelper::map(\backend\models\Admission::find()->all(),'id','admission_name'),
		                      [
		                      'prompt'=>'Select admission.',
		                      'required'=>TRUE,
		                      'onChange'=>'loadGroup();',
		                  ])?>
		                  
		                </div>
	                   	<div class="col-md-3">
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
			                                      $("#studentsemesterstatushistory-sssh_clslvl_relid" ).html( data ).trigger("change");
			                                      loadGroup();       
			                                }); ',

		                    	])
	                    	?>

	                   	</div>

                      	<div class="col-md-3">

	                       <?= $form->field($model, 'sssh_acadYear_relId')->dropdownList(
	                            ArrayHelper::map(\backend\models\SchoolYear::find()->all(),'id','sch_year_desc'),
	                            [
	                            'prompt'=>'Select academic year.',
	                            'onChange'=>'
	                            	loadGroup();
	                              // $.post("/w-group-setup/get-group-lists-peryear",
	                              //   {
	                              //     acadYearId:$("#studentsemesterstatushistory-sssh_acadyear_relid").val(),
	                              //     semId:$("#studentsemesterstatushistory-sssh_sem_relid").val(),
	                              //     admId:$("#studentsemesterstatushistory-admission").val(),
	                              //     progId:$("#studentsemesterstatushistory-program").val(),
	                              //     clslvlId:$("#studentsemesterstatushistory-sssh_clslvl_relid").val(),
	                              //     //deptId:$("#studentsemesterstatushistory-department").val(),
	                              //     specId:$("#studentsemesterstatushistory-spec").val()
	                              //   }, 
	                              //   function( data ) {
	                              //       //console.log("Returned: "+data);
	                              //       $("select#studentsemesterstatushistory-group" ).html( data ).trigger("change");
	                              //   }); 
	                            ',

	                            
	                        ])?>
                       	</div>

                       	<div class="col-md-3">
	                        <?= $form->field($model, 'sssh_sem_relId')->dropdownList(
	                            ArrayHelper::map(\backend\models\Semester::find()->all(),'id','semester_name'),
	                            [
	                            'prompt'=>'Select semester.',
	                            'onChange'=>'
	                            	loadGroupReal();
	                            ',
	                        ])?>
                       	</div>
                    </div>
                    <div class='row'>
                       	<div class="col-md-3">
	                        <?= 
	                        $form->field($model, 'department')->dropdownList(
	                          ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_name'),
	                            [
	                            'prompt'=>'Select Department.',
	                            'required'=>TRUE,
	                            'onChange'=>'$.post("/specialization/listspec-cp",
                                  {
                                    acadYearId:$("#studentsemesterstatushistory-sssh_acadyear_relid").val(),
                                    semId:$("#studentsemesterstatushistory-sssh_sem_relid").val(),
                                    admId:$("#studentsemesterstatushistory-admission").val(),
                                    progId:$("#studentsemesterstatushistory-program").val(),
                                    clslvlId:$("#studentsemesterstatushistory-sssh_clslvl_relid").val(),
                                    deptId:$(this).val(),
                                    specId:$("#studentsemesterstatushistory-spec").val()
                                  }, 
                                  function( data ) {
                                      //console.log("Returned: "+data);
                                      $("#studentsemesterstatushistory-spec" ).html( data );
                                      loadGroup();
                                  }); ',
	                        ])
	                        ?>

                       	</div>
                       	
                       	<div class="col-md-3">
	                        <?= 
	                        $form->field($model, 'sssh_clslvl_relId')->dropdownList(
	                          [],
	                            [
	                            'prompt'=>'Select Year.',
	                            'required'=>TRUE,
	                            'onChange'=>'loadGroup();',
	                        ])?>

                       	</div>
                       	<div class="col-md-3">
	                        <?= 
	                        $form->field($model, 'group')->dropdownList(
	                          [],
	                          [
	                            'prompt'=>'Select Major.',
	                            'onChange'=>'loadGroup();',
	                        ])?>

                       	</div>
                       	<div class="col-md-2"><br>             
	                        <button class="btn btn-success btn-sm" onclick="loadInsts();return false;" style="margin-top: 5px;">Load Grades</button>
	                       </div>
                      	</div>
	                   	<?php ActiveForm::end(); ?>
                   </div>
			</div>
			<div class="panel-body">
				<div class="container-fluid">
					<div class="row">
						<div class="col-md-12">
							<i id="printIcon" style="cursor: pointer; float: right; color: blue;" title="Print" onclick="printCont('selectionFormContainer')">Print<span class="glyphicon glyphicon-print"></span></i>
							<div class="container-fluid" id="groupCourseInstContainer" style="border: 1px dashed black;">
								
							</div>
						</div>
					</div>
				</div>
			</div>
		</div>
	</div>
</div>

<script type="text/javascript">

	window.printCont = (hj)=>{
		// $('#printIcon').hide();
        myWindow = window.open(
          '/grade-subission-form/get-report-print', 
          'title=Grade Summery', 
          'height=600,width=1300');

        myWindow.print();
        return false;
	}
	window.loadGroup = function(){
    	loadInsts();
    }
	window.loadInsts = function(){

		loadGroupReal();

		$('#groupCourseInstContainer').html('Loading Report...');
		admission = $('#studentsemesterstatushistory-admission').val();
		program = $('#studentsemesterstatushistory-program').val();
		department = $('#studentsemesterstatushistory-department').val();
		clslvl = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();

		acadYear = $('#studentsemesterstatushistory-sssh_acadyear_relid').val();
		semester = $('#studentsemesterstatushistory-sssh_sem_relid').val();
		spec = $('#studentsemesterstatushistory-spec').val();
		limit = $('#studentsemesterstatushistory-limit').val();

		$('#groupCourseInstContainer').html('Loading Grade Summery...');
		$.post('/grade-subission-form/get-report',
			{
				adm:admission,
				prog:program,
				dept:department,
				acadYear:acadYear,
				sem:semester,
				spec:spec,
				clslvl:clslvl,
				limit:limit
			})
		.done(function(data){
			$('#groupCourseInstContainer').html(data);
		});
	}

	window.loadGroupReal = function(){
		$.post("/w-group-setup/get-group-lists-peryear",
            {
              acadYearId:$("#studentsemesterstatushistory-sssh_acadyear_relid").val(),
              semId:$("#studentsemesterstatushistory-sssh_sem_relid").val(),
              admId:$("#studentsemesterstatushistory-admission").val(),
              progId:$("#studentsemesterstatushistory-program").val(),
              clslvlId:$("#studentsemesterstatushistory-sssh_clslvl_relid").val(),
              //deptId:$("#studentsemesterstatushistory-department").val(),
              specId:$("#studentsemesterstatushistory-spec").val()
            }, 
            function( data ) {
                $("select#studentsemesterstatushistory-group" ).html( data ).trigger("change");
            }); 
	}
</script>