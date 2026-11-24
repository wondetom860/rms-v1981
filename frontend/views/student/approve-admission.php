<?php 
use yii\bootstrap\Modal;

$this->title = "Approve student admission";
$this->params['breadcrumbs'][] = $this->title;

echo "<h3>{$this->title}</h3>";

if ($models && count($models) > 0) {
	// display Table here with list of new admitted students.

	echo "<table class='table table-condensed table-stripped table-hover table-sm'>
		<thead>
			<th>#</th>
			<th>Reg. ID</th>
			<th>Full Name</th>
			<th>Program</th>
			<th>Year of Admission</th>
			<th>Department</th>
			<th>Specialization</th>
			<th>Status</th>
		</thead>
		<tbody>
		";
	$count = 0;
	foreach ($models as $student) {
		$dept = $student->std_dept_relId ? $student->stdDeptRel->dept_name : ' - ';
		$spec = $student->std_spec_relId ? $student->stdSpecRel->spec_name : ' - ';
		$btn = $student->stdGroupReal ? "{$student->stdGroupReal->getGsDisp()}" : "Pending section assignmrnt";
		echo "<tr>
				<td>".++$count."</td>
				<td>{$student->std_regId}</td>
				<td><a href='#' onclick='loadMoreofStudent({$student->id})'>{$student->getFullName()}</a></td>
				<td>{$student->getAdmissionProgram()}</td>
				<td>{$student->stdAcadYearRel->sch_year_desc}</td>
				<td>{$dept}</td>
				<td>{$spec}</td>
				<td>{$btn}</td>
			</tr>";
	}
	echo "</tbody>
	</table>";
}else{
	echo "<p class='bg-warning'>No record.</p>";
}

?>
<script type="text/javascript">
	window.approveAdmision = (studentId) => {
		// 
	}
	window.loadMoreofStudent = (studentId) =>{
		$('#modal').find('.modal-header').html('Student Profile Information');
        $('#modalContent').css('text-align','left');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $('#modalContent').html('Loading Student Information');
        $.get('/student/view?id='+studentId,{}).done(function(data){
            $('#modalContent').html(data);
        });
	}
</script>
<?php

 Modal::begin([
    'header'=>'<h4>Student Admission Information</h4>',
    'id'=>'modal',
    'size'=>'modal-lg',
    'options'=>[
        'style'=>[
            'text-align'=>'left',
        ],
    ],
 ]);
 echo "<div id='modalContent' class='jumbotron'>Loading user account form...</div>";
 Modal::end();
 ?>


