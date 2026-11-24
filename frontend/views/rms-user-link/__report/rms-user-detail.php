<?php 


if (isset($model)) {
	$user = $model->rmsUserRel;
	echo "<center>";
	echo "<img class='img-circle' src='/uploads/{$user->photo_num}' height='180' width='120' alt='user photo'> <br>";

	echo "<small>{$user->getFullName()}</small><br>";
	if (!is_null($model->rms_std_id) && !empty($model->rms_std_id)) {
		echo "<small>Student >> {$model->rmsStdRel->std_regId} >> {$model->rmsStdRel->stdDeptRel->dept_name}</small><br>";
	}

	if (!is_null($model->rms_inst_id)  && !empty($model->rms_inst_id)) {
		echo "<small>Instructor >>{$model->rmsInstRel->instDepRel->dept_name}</small><br>";
	}
	echo "</center>";
}


?>