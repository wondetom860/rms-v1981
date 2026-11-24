<?php
	
	$this->title = "Submit thesis title";
	$srd2Id = 0;
?>
<!-- list courses with thesis title -->
<div class="container-fluid">
	<div class="container-fluid">
		<p>Submit new title : </p>
		<label for="select_srd2">Choose Project/Thesis Work Course:</label>
		<select id="select_srd2" onchange="updateEntry($(this).val())">
			<option value="" selected>--Select Course--</option>
			<?php foreach ($projectSrd2s as $srd2){
				echo "<option value='{$srd2->id}'>{$srd2->srd2CourseRel->course_title}</option>";
			}?>
		</select>
		
		<br>
		<label for="titleInput">Enter You Project/Thesis Work Title</label>
		<textarea rows="2" id="titleInput" class="form-control"></textarea><br>
		<button class="btn btn-primary btn-sm pull-right" onclick="submitTitle()" >Submit</button>
	</div>
	<hr>
	<div class="container-fluid" id="reportContainer">
		<!-- Title submitted by the student. -->
		<?php
		echo "<input type='hidden' value = {$srd2Id} id='srd2_id' />";
		if ($projectSrd2s && count($projectSrd2s) > 0) {
			$c = 0;
			foreach ($projectSrd2s as $srd2) {
				foreach ($srd2->thesisTitles as $Tkey) {
					$file = file_exists("uploads/thesisTitles/".$Tkey->tt_title) ?  file_get_contents("uploads/thesisTitles/".$Tkey->tt_title) : "-";
					
					if(empty($file)){continue;}
					$sts = $Tkey->tt_approved ? 'success':'warning';
					echo "<div class='alert alert-{$sts}'>";
					echo ++$c.".<b>{$srd2->srd2CourseRel->course_title}</b> : {$file}<br>";
					echo "</div>";	
				}
			}
		}else{
			// var_dump($titles);die();
			echo "<p class='bg-danger'>Thesis/Project course not enrolled yet.</p>";
		}

		?>
	</div>
</div>

<script type="text/javascript">
	__srd2Id=0;
	const updateEntry = (srd2Id) => {
		console.log(srd2Id);
		$('#srd2_id').val(srd2Id);
		__srd2Id = srd2Id;
		console.log($('#srd2_id').val());
	}
	window.submitTitle = function(){
		title = $('#titleInput').val();
		srd2Id = $('#srd2_id').val();
		srd2Id2 = $("#select_srd2 :checked").value;
		console.log(srd2Id,srd2Id2);
		if (title == '' || title == ' ') {
			$('#titleInput').focus();
		}else{
			$.post('/upload-thesis-title/upload-title',{title:title,srd2Id:__srd2Id}).done(function(data){
				if(data == 1){
					__srd2Id=0;
			    	window.location.href = window.location }
				else{
					$('#reportContainer').html(data);
					$('#titleInput').focus();
				} 
			});
		}
	}
</script>