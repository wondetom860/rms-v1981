<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$model = new \backend\models\StudentSemesterStatusHistory();


?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-4">
      <?= $form->field($model, 'admission')->dropdownList(
          ArrayHelper::map(\backend\models\Admission::find()->all(),'id','admission_name'),
          [
          'prompt'=>'',
          'required'=>TRUE,
      ])->label('Admission') ?>
      
    </div>
   	<div class="col-md-4">
    	<?= $form->field($model, 'program')->dropdownList(
            ArrayHelper::map(\backend\models\Program::find()->all(),'id','program_name'),
                [
                'prompt'=>'',
                'required'=>TRUE,
        	])->label('Program') 
    	?>

   	</div>

    <div class="col-md-4">
       <?= $form->field($model, 'acadYearId2')->dropdownList(
            ArrayHelper::map(\backend\models\SchoolYear::find()->all(),'id','sch_year_desc'),
            [
            'prompt'=>'',
        ])->label('Academic Year') ?>
    </div>
  </div>
  <div class="row">
    <div class="col-md-4">
       <?= $form->field($model, 'sssh_sem_relId')->dropdownList(
        ArrayHelper::map(\backend\models\Semester::find()->all(), 'id', 'semester_name'),
              [
              'prompt'=>'',
          ])->label('Semester') ?>
      </div>
      <div class="col-md-6">
         <?= $form->field($model, 'department')->dropdownList(
          ArrayHelper::map(\backend\models\Department::find()->all(), 'id', 'dept_name'),
                [
                'prompt'=>'',
            ])->label('Department') ?>
        </div>
   	<div class="col-md-2 pull-right"><br>
   		<button class="btn btn-sm btn-primary pull-right" onclick="loadList();return false;">Get Record</button>
   	</div>
</div>

<?php ActiveForm::end(); ?>

<script type="text/javascript">

	// window.approveRecords = (recordIds)=>{
	// 	$.post('/graduate-student/approve-records',{recordIds:recordIds}).done((data)=>{
	// 		if (data == 1) { window.location.href = window.location; }
	// 		alert(data);
	// 	});
	// }
	// window.approveRecord = (recordId)=>{
	// 	$.post('/graduate-student/approve-record',{recordId:recordId}).done((data)=>{
	// 		if (data == 1) { window.location.href = window.location; }
	// 		alert(data);
	// 	});
	// }
	window.loadList = ()=>{
		$("#listContainer" ).html('Loading record....');
		$.post("/clearance-allowed-student/get-student-list-for-adding",
            {
              acadYearId:$("#studentsemesterstatushistory-acadyearid2 :selected").html(),
              admId:$("#studentsemesterstatushistory-admission :selected").html(),
              semId:$("#studentsemesterstatushistory-sssh_sem_relid :selected").html(),
              progId:$("#studentsemesterstatushistory-program :selected").html(),
              deptId:$("#studentsemesterstatushistory-department :selected").html(),
              specId:$("#studentsemesterstatushistory-spec :selected").html(),
              clslvl:$("#studentsemesterstatushistory-clslvl :selected").html()
            }, 
            function( data ) {
                $("#listContainer" ).html( data );
            }); 
	}
</script>