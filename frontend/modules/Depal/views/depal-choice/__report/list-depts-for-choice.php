<?php 

if (isset($settingModel) && !empty($settingModel)) {
	$departments = json_decode($settingModel->elligible_depts);

	if ($departments) {
		echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
		echo "<caption style='font-weight:normal; color: #000; text-align:center;'>
				Choose departments in order of your interest :
			</caption>";
		echo "<thead>
				<th>#</th>
				<th>Code</th>
				<th>Department Name</th>
				<th>Choose</th>
				<th>Remark</th>
			";
		echo "</thead><tbody>";
		$count = 0;
		foreach ($departments as $dept) {
			$department = \backend\models\Department::findOne($dept);
			if ($department) {
				$d = "{$department->dept_code}";
				$input = "<input type='checkbox' id='dept__{$department->id}' data-code='{$d}' onchange='addToChoice2({$dept}); return false;' />";
				echo "<tr>";
				echo "
					<td>".++$count."</td>
					<td>{$department->dept_code}</td>
					<td>{$department->dept_name}</td>
					<td style='text-align: center;'>{$input}</td>
					<td></td>
					";
				echo "</tr>";
			}
		}
		echo "</tbody></table>";
	}else{
		echo "<p class='bg-warning'>Eligible departments list empty</p>";
	}
}else{
	echo "<p class='bg-danger'>No record found.</p>";
	return;
}

?>

<script type="text/javascript">
	__deptList = [];
	max_count = <?= $settingModel->depts_count ?>;
	window.addToChoice2 = (deptId) => {
		r = 0;
		if(jQuery.inArray(deptId,__deptList) !== -1){ 
            // if item is already selected, remove it because user deselected it.
            __deptList.splice($.inArray(deptId,__deptList),1);
            r= 0;
        }else{//going to add the item to the list.
        	if (__deptList.length >= max_count) {
				$('#dept__'+deptId).prop({'checked':false});
				alert("You can choose at most "+max_count+" departments.");
				r = 0;
			}else{
				__deptList.push(deptId);
            	r= 1;
			}
        }
        $('#depalchoice-dept_selected').val(JSON.stringify(__deptList));
        $('#dept__'+deptId).prop({'checked':(r == 1)});
		$("#choices").text(JSON.stringify(findDeptCode(__deptList)));
	}

	window.findDeptCode = (__deptList) => {
		code = [];
		$.each(__deptList,(index,dept) => { code.push($("#dept__"+dept).data('code')); });
		return code;
	}
</script>