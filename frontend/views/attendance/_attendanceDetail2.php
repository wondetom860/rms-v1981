<?php

if (is_object($model) && $model instanceof \backend\models\Attendance ) {
	
}else{
	throw new Yii\web\NotFoundHttpException("Students List Error!", 1);
}

?>

<table class="table table-bordered table-condensed table-striped table-hover">
	<thead style="background-color: #89F">
		<th>#</th>
		<th>Student Name</th>
		<th>Reg. ID</th>
		<th>Status</th>
	</thead>
	<tbody>
		<?php 
		$count = 0;
		$stdModel = $model->getPresentStudent();
		if ($stdModel) {
			foreach ($stdModel as $key) {
				$name = $key->stdUserRel;
				echo "
				<tr>
					<td>". ++$count ."</td>
					<td>{$key->std_regId}</td>
					<td>{$name->first_name} {$name->middle_name} {$name->last_name}</td>
					<td>P</td>
				</tr>
				";
			}
		} ?>
	</tbody>
</table>
