<?php 

if (isset($model) && !empty($model) && count($model) > 0) {
	echo "<table class='table table-sm table-condensed table-bordered table-hover'>";
	echo displayHeader($model[0]);
	echo "<tbody>";
	$detpSelection = getDeptForSelection();
	foreach ($model as $keyModel) {
		echo displayContent($keyModel,$detpSelection);
	}

	echo "<tr><td colspan = 3><button class='btn btn-xs btn-warning pull-right' onclick='saveSelection();return false;'>Save</button></td></tr>";

	echo "</tbody></table>";
}else{
	echo "<p class='bg-danger'>No Record Available</p>";
}


function displayContent($keyModel,$detpSelection){
	$gpa = $keyModel->getLastGpas();
	$sgpa = $gpa ? sprintf("%0.2f",$gpa[0]) : ' - ';
	$cgpa = $gpa ? sprintf("%0.2f",$gpa[1]) : ' - ';
	$rep = "<tr><td>{$keyModel->studentRel->std_regId}</td>";
	$rep .= "<td><a href='#' onclick='showStudentInfoView({$keyModel->student_relId});return false;' style='color:#000' >{$keyModel->getStudentFullName()}</a></td>";
	$rep .= "<td>{$keyModel->studentRel->std_type}</td>";
	$rep .= "<td style='text-align:center; font-weight:bold;'>{$sgpa}</td>";
	$rep .= "<td style='text-align:center; font-weight:bold;'>{$cgpa}</td>";
	$deptChoice = json_decode($keyModel->dept_choice);
	$j = 0;
	$btnCance = "<button class='btn btn-xs btn-warning pull-right' onclick='cancelSelection({$keyModel->id}); return false;' title='Cancel Department Assignment'>X</button>";
	$btnEducBkgrnd = "<button class='btn btn-xs btn-info' onclick='showEducBkgrnd({$keyModel->id}); return false;' title='Show Education Background Information'>Show</button>";
	$input = "<select class='form-control' onchange='setChoice({$keyModel->id},$(this).val());return false;'>";
	$input .= $detpSelection;
	while ( $j < $keyModel->settingRel->max_count_dept) {
			$dept = is_array($deptChoice)&&isset($deptChoice[$j]) ? $deptChoice[$j] : NULL;
			$department = \backend\models\Department::findOne($dept);
			$dcode  = $department ? $department->dept_code : "";
			$depId  = $department ? $department->id : "";
			$deptName = !empty($dcode) ? $department->dept_name : '';
			$j++;
			$rep .= "<td title='{$deptName}'>{$dcode}</td>";
		}
	$input .= "</select>";
	$input = is_null($keyModel->approved_dept_relId) ? $input : $keyModel->approvedDeptRel->dept_name.$btnCance;
	$rep .= "<td>{$input}</td>";
	$rep .= "<td>{$btnEducBkgrnd}</td>";
	$rep .= "</tr>";
	return $rep;
}

function getDeptForSelection(){
	$deptSelection = "<option value=> - </option>";
	$depts = \backend\models\Department::find()->orderBy('dept_name')->all();
	if ($depts && count($depts) > 0) {
		foreach ($depts as $keyDept) {
			$deptSelection .= "<option value='{$keyDept->id}'> {$keyDept->dept_name} </option>";
		}
	}
	return $deptSelection;
}


function displayHeader($model){
	$rep = "
		<caption style='color:#000; text-align:center; font-weight:bold;'>
			<h3>{$model->getSettingENtry()}</h3>
		</caption>
		<thead style='background-color:#a2d246;'>
			<tr>
				<th colspan=3 style=' text-align: center;'>Student Information</th>
				<th colspan=2 style=' text-align: center;'>Last GPA</th>
				<th colspan={$model->settingRel->max_count_dept} style=' text-align: center;'>Department Choice</th>
				<th rowspan=2 style=' text-align: center;'>Option</th>
				<th rowspan=2 style=' text-align: center;'>More</th>
			</tr>
			<tr>
				<th style=' text-align: center;'>Reg. ID</th>
				<th style=' text-align: center;'>Full Name</th>
				<th style=' text-align: center;'>Type</th>
				<th style=' text-align: center;'>SGPA</th>
				<th style=' text-align: center;'>CGPA</th>
			";
			$j = 0;
			while ( $j < $model->settingRel->max_count_dept) {
				$j++;
				$kj = $j == 1?'1<sup>st</sup>' :($j == 2 ? '2<sup>nd</sup>' : ($j == 3?'3<sup>rd</sup>':$j.'<sup>th</sup>'));
				$rep .= "<th>{$j}</th>";
			}
		$rep .= "</tr></thead>
		";

	return $rep;
}
?>