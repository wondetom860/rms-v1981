<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

$model = new \backend\models\StudentSemesterStatusHistory();


?>
<?php $form = ActiveForm::begin(); ?>
<div class="row">
    <div class="col-md-3">
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
        	])->label('Program') 
    	?>

   	</div>

  	<div class="col-md-3">
     <?= $form->field($model, 'department')->dropdownList(
      ArrayHelper::map(\backend\models\Department::find()->all(), 'id', 'dept_name'),
            [
            'prompt'=>'Select department',
        ])->label('Department') ?>
  	</div>


  	<div class="col-md-2">

       <?= $form->field($model, 'acadYearId2')->dropdownList(
            ArrayHelper::map(\backend\models\GraduateStudent::find()->groupBy('gstd_grad_year')->all(),'id','gstd_grad_year'),
            [
            'prompt'=>'',
        ])->label('Academic Year') ?>
   	</div>
   	<div class="col-md-2 pull-right"><br>
   		<button class="btn btn-sm btn-primary pull-right" onclick="loadList();return false;">Get Record</button>
   	</div>
</div>

<?php ActiveForm::end(); ?>

<script type="text/javascript">

  gstdList =[];
	window.approveRecords = (recordIds)=>{
    if (confirm('Are you sure to approve records?')) {
  		$.post('/graduate-student/approve-records',{recordIds:recordIds}).done((data)=>{
  			if (data == 1) { window.location.href = window.location; }
  			alert(data);
  		});
    }
	}
	window.approveRecord = (recordId)=>{
		if (confirm('Are you sure to approve record?')) {
      $.post('/graduate-student/approve-record',{recordId:recordId}).done((data)=>{
        if (data == 1) { window.location.href = window.location; }
        alert(data);
      });
    }
	}
	window.loadList = ()=>{
		$("#listContainer" ).html('Loading record....');
		$.post("/graduate-student/get-student-list-for-approval",
            {
              acadYearId:$("#studentsemesterstatushistory-acadyearid2 :selected").html(),
              admId:$("#studentsemesterstatushistory-admission").val(),
              progId:$("#studentsemesterstatushistory-program").val(),
              deptId:$("#studentsemesterstatushistory-department").val(),
              specId:$("#studentsemesterstatushistory-spec").val()
            }, 
            function( data ) {
                $("#listContainer" ).html( data );
            }); 
	}
</script>