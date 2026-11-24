<?php 
	
if ($academicWorkDay && count($academicWorkDay) > 0) {
	echo "
		<table class='table table-xs table-condensed table-bordered table-sm'>
		<caption style='font-weight: bold; color: #000; text-align: center; background-color:#FDE09A;'>Class Schedule</caption>
		<thead style='background-color:#a6df03;'>
			<th>DAY/TIME</th>
	";

	$justOne = $academicWorkDay[0];

	$classTimeDistrib = $justOne->csClassTimeDistributions;
	foreach ($classTimeDistrib as $key) {
		$class = $key->luncTime() ? 'warning' : ($key->nowTime() ? 'info':'active') ;
		echo "
			<th style='text-align:center;' class='bg-{$class}'>{$key->classCount()}<br><small style='font-size:7pt;'>{$key->getStartTime()}-{$key->getEndTime()}</small></td>
			";
	}
	echo "</thead><tbody>";
	$modelClassSchedule = new \frontend\modules\ClassSchedule\models\CsClassScheduleDetail();

	foreach ($academicWorkDay as $key) {
		$title = "";
		$dists = $key->csClassTimeDistributions;
		$bgToday = $key->isToday() ? 'info' : 'active';
		echo "<tr>";
		echo "<td class='bg-{$bgToday}' style='background-color:#FDE88A;'>{$key->day_name}</td>";
		foreach ($dists as $keyDist) {
			$class = $keyDist->luncTime() ? 'warning' : 'active';
			// if ($keyDist->nowTime() && $keyDist->workDayRel->isToday()) {
			// 	$class = 'success';
			// }
			// $schedules = $keyDist->getCsClassScheduleDetails($coffdetModel,$type,$room);
			$schedules = $modelClassSchedule->getSchedule($coffdetModel->id,$keyDist->id,$room);
			if (is_string($schedules)) {
				if ($keyDist->luncTime()) {
					$scheds = "";
				}else{
					$scheds = "<br>";
					$class = 'error';
					$title = \yii\helpers\Html::encode($schedules);
				}
				
			}else{
				if ($schedules && count($schedules) > 0) {
					$class = 'danger';
					$scheds = renderableSchedule($schedules);
				}else{
					if ($keyDist->luncTime()) {
						$scheds = "";
					}else{
						// check if all the hour is scheduled. [credithr-lecture-lab-tutorial]
						if ($coffdetModel->haveExtraScheduleTime($type)) {
							$scheds = "<button class='btn btn-block btn-success' onclick='setSchedule({$keyDist->id},{$coffdetModel->id},\"$type\",{$room},{$coffdetModel->coff2detCoff2Rel->coff2_group_relId});return false;'>Set</button>";
						}else{
							$scheds = "<p class='bg-success'>All ".ucfirst($type)." hour is scheduled.</p>";
						}
					}
				}
			}
			echo "<td class='bg-{$class}' title='{$title}' toolTip='{$title}'>{$scheds}</td>";
		}
		echo "</tr>";
	}

	echo "</tbody></table>";
}else{
	echo "<p class='bg-error'>No AcademicWorkDay entry found.</p>";
}


function renderableSchedule($schedules){
	$sc = "";
	foreach ($schedules as $keySchedule) {
		if (Yii::$app->user->can('DepartmentHead') && $keySchedule->created_by == Yii::$app->user->identity->id) {
			$sc .= "<a href='#' onclick='updateForm({$keySchedule->id});return false;'><small><center style='border-bottom:1px solid black;'>
				{$keySchedule->coff2detRel->coff2detCourseRel->course_code}/
				{$keySchedule->coff2detRel->coff2detCoff2Rel->coff2GroupRel->gsGroupRel->group_name}/
				{$keySchedule->roomRel->room}<br>
				<i>{$keySchedule->class_type} - {$keySchedule->coff2detRel->coff2detInstRel->getFullName_av()}</i>
			</center></small><br></a>";
		}else{
			$sc .= "<small><center style='border-bottom:1px solid black;'>
				{$keySchedule->coff2detRel->coff2detCourseRel->course_code}/
				{$keySchedule->coff2detRel->coff2detCoff2Rel->coff2GroupRel->gsGroupRel->group_name}/
				{$keySchedule->roomRel->room}<br>
				<i>{$keySchedule->class_type} - {$keySchedule->coff2detRel->coff2detInstRel->getFullName_av()}</i>
			</center></small><br>";
		}
	}

	return $sc;
}

?>


<script type="text/javascript">
	window.updateForm = (scheduleId)=>{
		$('#modal').find('.modal-header').html('Update time schuduling entry');
        $('#modalContent').css('text-align','left');
        $('#modalContent').html('Loading form data...');
        $('#modal').find('.modal-dialog').removeClass('modal-sm').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/ClassSchedule/cs-class-schedule-detail/update',{id:scheduleId}).done(function(data){
            $('#modalContent').html(data);
        });
	}
	window.setSchedule = (schedDistId,coffdetId,type,room,group) =>{
		if (confirm('Set class schedule?')) {
			$.post('/ClassSchedule/cs-class-schedule-detail/create2',{schedDistId :schedDistId,coffdetId:coffdetId,type:type,room:room,group:group}).done((resp) =>{ 
			 if (resp == 1) {
			 	loadForm();
			 } else{
			 	$('.detail-container').html(resp);
			 	$('.detail-container').focus(); 
			 }
			})
		}

	}
</script>

<!-- <button te ></button> -->