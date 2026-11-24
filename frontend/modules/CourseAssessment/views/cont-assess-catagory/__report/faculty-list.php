<?php 
$coff2details = \backend\models\CourseOffering2Detail::find()
	->innerJoin('course_offering2','course_offering2.id=course_offering2_detail.coff2det_coff2_relId')
	->where([
		'coff2det_course_relId' => $model->coff2det_course_relId,
		'course_offering2.coff2_acadYear_relId' => $model->coff2detCoff2Rel->coff2_acadYear_relId,
		'course_offering2.coff2_sem_relId' => $model->coff2detCoff2Rel->coff2_sem_relId,
	])
	->all();

if ($coff2details && count($coff2details) > 0) {
	echo "<table class='table table-condensed table-striped'>";
	echo "<caption style='color:#000; font-weight:normal; text-align: center;'>
			<h4>Instructor course assignment</h4>
		</caption>";
	echo "<thead style='background-color:#FD9;'>
			<th>#</th>
			<th>Staff Name</th>
			<th>Role</th>
			<th>Remark</th>
		</thead><tbody>";
	$count = 0;
	foreach ($coff2details as $coff2det) {
		$facultyforSection = $coff2det->facultyAssignments;
		if (count($facultyforSection) > 0) {
			// code...
		}else{
			continue;
		}
		$btnRmv = "";
		echo "<tr><td colspan=4 style='text-align: center; background-color: #ACE;'>Section/Group: {$coff2det->coff2detCoff2Rel->coff2GroupRel->getGsDisp()}</td></tr>";
		foreach ($facultyforSection as $facluty) {
			// if (!$facluty->category) {
			// 	$btnRmv = "<button class='btn btn-xs btn-warning pull-right' title='Delete' onclick='DeleteStaffAssignment({$facluty->id});return false;'>Delete</button>";
			// }
			$cat = $facluty->category ? "<span style='color:red;'>*</span>":'';
			echo "<tr>";
			echo "
				<td> </td>
				<td>{$facluty->instRel->getFullName()}{$cat}</td>
				<td>{$facluty->assign_as}</td>
				<td>{$btnRmv}</td>";
			echo "</tr>";
		}
	}
	echo "</tbody></table>";
}

// if (isset($model)) {
// 	$facultyforSection = $model->facultyAssignments;
// 	$btnRmv = "";
// 	if ($facultyforSection && count($facultyforSection) > 0) {
// 		if (Yii::$app->user->can('DepartmentHead')) {
// 			if (($model->coff2detAcProgRel->status == $model->coff2detAcProgRel::STATUS_ACTIVE)) {
// 				$btnMore = "<button class='btn btn-xs btn-warning pull-right' title='Add more staff for this offering' onclick='addMore({$model->coff2det_relId});return false;'>Add more staff</button>";
// 			}else{
// 				$btnMore = "<span class='bg-warning pull-right' style='font-style:italic;font-size:8pt;'>ACY is not active</span>";
// 			}
// 		}else{
// 			$btnMore = "<span class='bg-warning pull-right' style='font-style:italic; font-size:8pt;'>HoD can add more faculty to this offering.</span>";
// 		}
		
// 		echo "<table class='table table-condensed table-striped'>";
// 		echo "<caption style='color:#000; font-weight:bold; text-align: left;'>
// 				Facluty assignment for section{$btnMore}
// 			</caption>";
// 		echo "<thead style='background-color:#FD9;'>
// 				<th>#</th>
// 				<th>Staff Name</th>
// 				<th>Role</th>
// 				<th>Remark</th>
// 			</thead><tbody>";
// 		$count = 0;
// 		foreach ($facultyforSection as $facluty) {
// 			// if (!$facluty->category) {
// 			// 	$btnRmv = "<button class='btn btn-xs btn-warning pull-right' title='Delete' onclick='DeleteStaffAssignment({$facluty->id});return false;'>Delete</button>";
// 			// }
// 			$cat = $facluty->category ? "<span style='color:red;'>*</span>":'';
// 			echo "<tr>";
// 			echo "
// 				<td>".++$count."</td>
// 				<td>{$facluty->instRel->getFullName()}{$cat}</td>
// 				<td>{$facluty->assign_as}</td>
// 				<td>{$btnRmv}</td>";
// 			echo "</tr>";
// 		}
// 		echo "</tbody></table>";
// 	}else{
// 		echo "Instructor assignment not made yet.";
// 	}
// }
?>

<script type="text/javascript">
	window.DeleteStaffAssignment = (modelId) => {
		if (confirm('Are you sure to delete record?')) {
			$.post('/faculty-course-assignment/delete?id='+modelId).done((resp) => {
				window.location.href = window.location;
			});
		}
	}
	window.addMore = (coff2detId) => {
		window.location.href = "/faculty-course-assignment/index";
		// $('#modal').find('.modal-header').html('Add more staff for the offering:');
        // $('#modalContent').css('text-align','left');
        // $('#modalContent').html('Loading data...');
        // $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        // $('#modal').modal('show');
        // $.get('/faculty-course-assignment/create',{coff2detId:coff2detId}).done(function(data){
        //     $('#modalContent').html(data);
        // });
	}
</script>