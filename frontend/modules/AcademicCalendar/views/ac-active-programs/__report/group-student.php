<?php 
use yii\helpers\Html;

if (isset($gsetModel) && isset($students)) {
	$gspec = $gsetModel->gs_spec_relId ? $gsetModel->gsSpecRel->spec_name : '';
	?>
	<div class='container-fluid table-responsive'>
		<div class="col-md-12" style="border-bottom: 1px solid black; vertical-align: middle; background-color: #2DB;">
			<div class="col-md-8" style="vertical-align: middle;"><br>
				<center><p class="pull-left">
					<?= $gsetModel->getGsDisp()." ".$gspec ?><br>
					<u><?= $gsetModel->getAcadYearHeader() ?></u>
					<b><?= $gsetModel->gsGroupRel->group_name ?></b>
					</p></center>
			</div>
			<div class="col-md-4 pull-right" style="font-size: 9pt; border-left: 1px solid black;">
				<label for="filter__">Filter:</label>
				<select class="form-control" onchange="updateFilter($(this).val());" id="filter__">
					<option selected="" value="0">Load All</option>
					<option value="2">New Entry</option>
					<option value="3">Previously grouped</option>
					<option value="4">Readmitted</option>
				</select>
				<button class="btn btn-xs btn-warning pull-right" title="Load only ungrouped students in this semester" onclick="updateFilter2(1)">Load ungrouped</button>
				<br>
			</div>	
		</div>
		<div class="col-md-12" id="studentListContainer" style="max-height:  400px; overflow: auto;">
			<?= $this->render('__table',['students' => $students,'gsetModel' => $gsetModel]) ?>
		</div>
		<div class="col-md-12">
			<hr>
			<span style="font-style: italic;" id="_selected_students_count"></span>
			<button class="btn btn-xs btn-primary pull-right" onclick="assignStudents(<?= $gsetModel->id?>);return false;">Submit</button>
		</div>
	</div>
	<?php
}else{
	echo "<p class='class-'bg-danger'>In-Sufficient record.</p>";
}

?>

<script type="text/javascript">
	__filterMode = 0;
	__loadOnlyUngrouped = 0;
	window.assignStudent = (studentId,gsetId) =>{
		status = addtoList(Number(studentId));
        $('#'+studentId+'_'+gsetId+'_00').prop({'checked':(status == 1)});
        $("#_selected_students_count").text('Students Selected : '+__StdList.length);
        // console.log(__StdList);
	}

	window.addtoList=function(studentId){
        r = 0;
        if(jQuery.inArray(studentId,__StdList) !== -1){ 
        	// if item is already selected, remove it because user deselected it.
            __StdList.splice($.inArray(studentId,__StdList),1);
            r= 0;
        }else{//going to add the item to the list.
            __StdList.push(studentId);
            r= 1;
        }
        // $('#acprogramschedule-__selected_programs').val(JSON.stringify(__StdList));
        return r;
    }

	window.ungroup = (gsetId,studentId) => {
		// ungroup student from the group:
		if (confirm('Are you sure to unlist student from this group? ')) {
			$.post('/w-group-assignment/degroup',{stdId:studentId,gsetId:gsetId}).done((resp) => {
				updateFilter(__filterMode);
			})
		}

	}

	window.assignStudents = (gsetId) => {
		if (confirm('Assign '+__StdList.length+' students to the group? ')) {
			$.post('/w-group-assignment/create',{gid:gsetId,stdList:JSON.stringify(__StdList)}).done((resp) => {
				updateFilter(__filterMode);
			})
		}
	}

	window.updateFilter2 = (laodOnlyUngroupedCheck) =>{
		__loadOnlyUngrouped = laodOnlyUngroupedCheck;
		updateFilter(__filterMode,__loadOnlyUngrouped);
	}
	window.updateFilter = (filterMode) => {
		__filterMode = filterMode;
		__StdList = [];
		
		$("#studentListContainer").html('filtering....');
		$("#_selected_students_count").text('Students Selected : '+__StdList.length);

		$.get('/AcademicCalendar/ac-active-programs/get-students',{gsetId:__gsetId,'filterMode':__filterMode,'loadOnlyUngrouped':__loadOnlyUngrouped,'renderPart':1}).done(function(data){
            $('#studentListContainer').html(data);
        });
		// groupStudents(__gsetId,filterMode);
	}
</script>
