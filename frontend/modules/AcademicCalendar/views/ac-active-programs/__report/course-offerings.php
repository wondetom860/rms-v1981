<?php 

if (isset($model) && !empty($model)) {
	$coffs = $model->coff2s;

	if ($coffs && count($coffs) > 0) {
		// course offering entry for sections exists
		echo $this->render('course-offering',['coff2s' => $coffs]);
	}else{
		$modelCoff = $model->coffTemp; //[course-ids]
        if ($modelCoff) {
            echo $this->render('courses_offered',['modelCoffTemp' => $modelCoff]);
        }
	}
}else{
	echo "<p class='bg-warning'>No record.</p>";
}

?>