<?php 


$this->title = "Generate Class Attendance Form";
$this->params['breadcrumbs'][] = ['label' => 'Attendances', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

 ?>

<div class="exam-attendance-sheet-index container-fluid">
	<h3>>>> <?= $this->title ?></h3>
	<div class="row" style="font-size: 9pt;">
		<button class="btn btn-xs btn-warning hidden" id="print_btn" onclick="printList();return false;">Print</button>
		<div class="col-md-5" id="semester_courses">
			<small class="fa fa-gear">Laoding</small>
		</div>
		<div class="col-md-7" id="attendance_sheet" style="background-color: #fff;">
			<small class="fa fa-gear">Select Course/Group to generate attendance form</small>
		</div>
	</div>
</div>

<?php 

$this->registerJs(
	"
	window.printList = ()=>{
		 window.open(
              '/attendance/get-attendance-form-report-print', 
              'Print Student Class Attendance Form', 
              'height=740,width=700');

	}
	window.loadStudent  = function(coffdetId,courseId,groupId){

		$.post('/attendance/get-class-attendance-form-report',{coffdetId:coffdetId,groupId:groupId,courseId:courseId}).done((resp) => {
			$('#attendance_sheet').html(resp);
			$('#print_btn').removeClass('hidden');
		});
	}
	$.post('/attendance/course-list',{},function(data){
        //console.log(data);
        $('#semester_courses').html(data);
        });
	",
	$this::POS_READY);

 ?>