<?php
/** @var yii\web\View $this */
$this->title = Yii::t('rms','MOVE ATTENDACE RECORD');
if ($model && count($model) > 0) {
	# code...
}else{
	throw new \yii\web\NotFoundHttpException("Error Processing Request", 1);
}
?>

<div class="container-fluid">
	<h3>Move ATTENDANCE_DETAIL_RECORD</h3>
	<table class="table table-bordered table-hover">
		<thead style="background-color: #8D9;">
			<th>#</th>
			<th>Attendance Submission Info</th>
			<th>Records in Detail</th>
			<th>Option</th>
			<th>List</th>
		</thead>
		<tbody>
			<?php
			$ids = [];
			$count = 0;
			foreach ($model as $key) {
				$ids[] = $key->id;
				$btn = '<button onclick="move('. $key->id .')" class="btn btn-xs btn-primary" >MOVE</button>';
				$date = date_format(date_create($key->a_createdAt),'d, M Y');
				$group = $key->aGroupRel->gsGroupRel->group_name;
				$course = $key->aCourseRel->course_title;
				$dcount = \backend\models\Attendancedetail::find()->where(['ad_attendance_relId' => $key->id])->count();
				$btndel = $dcount == 0? '':'<button onclick="del('. $key->id .')" class="btn btn-xs btn-warning" >DELETE</button>';
				echo "<tr>
						<td>". ++$count ."</td>
						<td>{$date}-{$group}-{$course}</td>
						<td>{$dcount}</td>
						<td>{$btn}{$btndel}</td>
						<td>". count(json_decode($key->a_presentStudentsList)) ."</td>
					</tr>";
			}
			$btnAll = '<button onclick="moveAll('. json_encode($ids) .')" class="btn btn-xs btn-primary" >MOVE ALL</button>';
			?>
			<tr><td colspan="4"></td><td><?= $btnAll ?></td></tr>
		</tbody>
	</table>
</div>

<?php
$this->registerJs(
	"

	window.moveAll = function(homeIds){
		if(confirm('Are you sure to DELETE Attendance record')){
			$.post('/move-attendance/move-all-all',{id:homeIds}).done(function(data){
				if($. isNumeric(data)){
					window.location.href = window.location;
				}else{
					alert('Error : '+data);
				}
			});
		}
	}
	window.del = function(homeId){
		if(confirm('Are you sure to DELETE Attendance record')){
			$.post('/move-attendance/del-root',{id:homeId}).done(function(data){
				if($. isNumeric(data)){
					window.location.href = window.location;
				}else{
					alert('Error : '+data);
				}
			});
		}
	}

	window.move = function(homeId){
		if(confirm('Are you sure to MOVE Attendance record')){
			$.post('/move-attendance/move-all',{id:homeId}).done(function(data){
				if($. isNumeric(data)){
					window.location.href = window.location;
				}else{
					alert('Error : '+data);
				}
			});
		}
	}
	",
	$this::POS_READY);
?>


