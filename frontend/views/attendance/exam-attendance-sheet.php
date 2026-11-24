<?php 


$this->title = "Generate Exam Attendance Sheet";
$this->params['breadcrumbs'][] = ['label' => 'Attendances', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;

 ?>

<div class="exam-attendance-sheet-index container-fluid">
	<h3>>>> <?= $this->title ?></h3>
	<div class="row">
		<label for="include_attendance_report" class="pull-right">Include Attendance report</label>&nbsp;&nbsp;
		<input type="checkBox" class="pull-right" name="include_attendance_report" id="include_attendance_report" value="0">
		<button class="btn btn-xs btn-warning hidden" id="print_btn" onclick="printList();return false;">Print</button>
		<div class="col-md-5" id="semester_courses">
			<small class="fa fa-gear">Laoding</small>
		</div>
		<div class="col-md-7" id="attendance_sheet" style="background-color: #fff;">
			<small class="fa fa-gear">Select Course to generate exam attendance sheet</small>
		</div>
	</div>
</div>

<?php 

$this->registerJs(
	"
	window.printList = ()=>{
		 window.open(
              '/attendance/get-attendance-sheet-report-print', 
              'Print Students Exam Attendance Sheet', 
              'height=640,width=700');

	}
	window.loadStudent  = function(coffdetId,courseId,groupId){
		inclcude_attendace_report = Number($('#include_attendance_report').prop('checked'));

		$.post('/attendance/get-attendance-sheet-report',{iar:inclcude_attendace_report,coffdetId:coffdetId,groupId:groupId,courseId:courseId}).done((resp) => {
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