<?php 

if (is_null($studentModel)) {
	echo "<p class='bg-warning'>No record found</p>";
	return;
}else{
	$rep = "
		<div class='continer-fluid'>
			<u><h5>{$studentModel->getFullName()}</h5></u>
			<label for='eth_group'>Ethnic Group</label>
			<select id='eth_group' class='form-control'>
			<option id='' value=''>--Select ethnic group</option>
		";
	foreach ($model as $ethGroup) {
		$rep .= "<option id='{$ethGroup->id}' value='$ethGroup->id'>{$ethGroup->name}</option>";
	}

	$rep .="</select><br>
		<button class='btn btn-xs btn-warning pull-right' onclick='submitFormethgroup({$studentModel->id}); return false;'>Submit</button><br>
	<hr>
	<p class='bg-warning' style='font-size: 8pt; font-style: italic;'>This information is managed by end user only, no one else can modify or manage it without the consent of end user. Please carefully fill it, once after you have filled it, further altering won't be allowed. </p>
	</div>
	";

	echo $rep;
	// return;
}

?>
<script type="text/javascript">
	window.submitFormethgroup = (stdId) => {
		ethgroup = $("#eth_group :selected").val();
		if (ethgroup == 'undefined' || ethgroup == '') {
			alert('Please select one');
			$("#eth_group").trigger('click');
			return;
		}else{
			console.log(ethgroup);
			if (confirm("Are you sure to submit the data?")) {
				$.post('/student/update-eth-group',{stdId: stdId,ethgroup: ethgroup}).done((rep) => {
					if (rep == 1) {
						window.location.href = window.location;
					}else{
						alert(rep);
					}
				});
			}
		}
		
	}
</script>