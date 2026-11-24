<?php 

if (isset($model)) {
	$facultyforSection = $model->coff2detRel->facultyAssignments;
	$btnRmv = "";
	if ($facultyforSection && count($facultyforSection) > 0) {
		if (Yii::$app->user->can('DepartmentHead')) {
			if (($model->coff2detRel->coff2detAcProgRel->status == $model->coff2detRel->coff2detAcProgRel::STATUS_ACTIVE)) {
				$btnMore = "<button class='btn btn-xs btn-warning pull-right' title='Add more staff for this offering' onclick='addMore({$model->coff2det_relId});return false;'>Add more staff</button>";
			}else{
				$btnMore = "<span class='bg-warning pull-right' style='font-style:italic;font-size:8pt;'>ACY is not active</span>";
			}
		}else{
			$btnMore = "<span class='bg-warning pull-right' style='font-style:italic; font-size:8pt;'>HoD can add more faculty to this offering.</span>";
		}
		
		echo "<table class='table table-condensed table-striped'>";
		echo "<caption style='color:#000; font-weight:bold; text-align: left;'>
				Facluty assignment for section{$btnMore}
			</caption>";
		echo "<thead>
				<th>#</th>
				<th>Staff Name</th>
				<th>Role</th>
				<th>Remark</th>
			</thead><tbody>";
		$count = 0;
		foreach ($facultyforSection as $facluty) {
			if (!$facluty->category) {
				$btnRmv = "<button class='btn btn-xs btn-warning pull-right' title='Delete' onclick='DeleteStaffAssignment({$facluty->id});return false;'>Delete</button>";
			}
			$cat = $facluty->category ? "<span style='color:red;'>*</span>":'';
			echo "<tr>";
			echo "
				<td>".++$count."</td>
				<td>{$facluty->instRel->getFullName()}{$cat}</td>
				<td>{$facluty->assign_as}</td>
				<td>{$btnRmv}</td>";
			echo "</tr>";
		}
		echo "</tbody></table>";
	}else{
		echo "Instructor assignment not made yet.";
	}
}
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
		$('#modal').find('.modal-header').html('Add more staff for the offering:');
        $('#modalContent').css('text-align','left');
        $('#modalContent').html('Loading data...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/faculty-course-assignment/create',{coff2detId:coff2detId}).done(function(data){
            $('#modalContent').html(data);
        });
	}
</script>