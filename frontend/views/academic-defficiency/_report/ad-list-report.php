<?php 
// die("rer");
use \backend\models\WGroupSetup;

	$gs = new WGroupSetup();
    $gsets = $gs->getGsets($admId,$progId,NULL,$clslvlId,$acadYearId,$semId);
    // getGsets($admId,$progId,$deptId,$clslvlId,$acadYear,$semId)

    $ext = false;
    if ($gsets && count($gsets) > 0) {
    	$clslvlObj = \backend\models\ClassLevel::findOne($clslvlId);
    	$semObj = \backend\models\Semester::findOne($semId);
    	$ayObj = \backend\models\SchoolYear::findOne($acadYearId);
    	$progObj = \backend\models\Program::findOne($progId);
    	$admObj = \backend\models\Admission::findOne($admId);

        $initialClassLevel = \backend\models\ClassLevel::find()->where(['clslvl_program_relId' => $progObj->id])->orderBy('clslvl_num')->all()[0];

        // var_dump($initialClassLevel);die();
        $wreport = "";

    	$ext = $admObj->admission_name == 'Extension'?true:false;

    	$colCount = $clslvlObj->clslvl_num * 3 * ($ext?3:2);
        $cl = $clslvlObj->getClslvlDisp();

    	$wreport .= "<table class='table table-striped table-hover table-condensed table-sm' style='font-size:9pt;'>";
    	$wreport .= "<caption style='font-weight: normal; color:black;'>
    		<center>
    			".strtoupper(\Yii::$app->params['client']).", ".strtoupper(\Yii::$app->params['college'])."<br>
                OFFICE OF REGISTRAR<br>
    			<b><u>List of Students with Academic Defficiency</u></b><br>

    			Semester {$semObj->semester_name} of Academic Year {$ayObj->sch_year_desc}<br>

    			Program : <u>{$admObj->admission_name} {$progObj->program_name} {$cl} Year</u>
    		</center>
    	</caption>";
    	$wreport .= "<thead style='background-color: #8D0;'>";
    	$wreport .= "<th rowspan = 4 style='border:1px solid black; vertical-align:middle;'>#</th>
    		<th rowspan = 4 style='border:1px solid black; vertical-align:middle; text-align:left;'>Reg. NO.</th>
    		<th rowspan = 4 style='border:1px solid black; vertical-align:middle; text-align:left;'>Full Name</th>
    		<th colspan = {$colCount} style='border:1px solid black; text-align:center;'>Semester Grade History</th>
    		<th style='border:1px solid black; text-align:center;'>NGs & Fs</th>
    		</thead>";
    	// Class Year row
    	$wreport .= "<thead  style='background-color: #8D0;'>
    			<th style='border:1px solid black;'></th>
    			<th style='border:1px solid black;'></th>
    			<th style='border:1px solid black;'></th>";
    	$cl = $initialClassLevel->clslvl_num;
    	$cd = $colCount / $clslvlObj->clslvl_num;
    	while ($cl <= $clslvlObj->clslvl_num) {
    		$wreport .= "<th colspan={$cd} style='text-align:center; border:1px solid black;'>Year - {$cl}</th>";
    		$cl++;
    	}
    	$wreport .= "<th style='text-align:center; border:1px solid black;'></th>";
    	$wreport .= "</thead>";
    	// Semesters Row
    	$wreport .= "<thead style='background-color: #8D0;'>
    			<th style='border:1px solid black;'></th>
    			<th style='border:1px solid black;'></th>
    			<th style='border:1px solid black;'></th>";
    	$cl = $initialClassLevel->clslvl_num;
    	while ($cl <= $clslvlObj->clslvl_num) {

    		$wreport .= "<th colspan=3 style='border:1px solid black; text-align:center;'>{$cl}-I</th>
    			  <th colspan=3 style='border:1px solid black; text-align:center;'>{$cl}-II</th>";
    		if ($ext) {
    			$wreport .= "<th colspan=3 style='border:1px solid black; text-align:center;'>{$cl}-III</th>";
    		}
    		$cl++;
    	}
    	$wreport .= "<th style='text-align:center; border:1px solid black;'></th>";
    	$wreport .= "</thead>";
    	// GPA row
    	$wreport .= "<thead style='background-color: #8D0;'>
    			<th style='border:1px solid black;'></th>
    			<th style='border:1px solid black;'></th>
    			<th style='border:1px solid black;'></th>";
    	$cl = $initialClassLevel->clslvl_num;
    	while ($cl <= $clslvlObj->clslvl_num) { 

    		// Sem - I
    		$wreport .= "<th style='border:1px solid black; text-align:center;'>SGPA</th>
    			  <th style='border:1px solid black; text-align:center;'>CGPA</th>
    			  <th style='border:1px solid black; text-align:left;'>Status</th>";

    		// Sem II
    		$wreport .= "<th style='border:1px solid black; text-align:center;'>SGPA</th>
    			  <th style='border:1px solid black; text-align:center;'>CGPA</th>
    			  <th style='border:1px solid black; text-align:left;'>Status</th>";
    		if ($ext) {
    			// summer
    			$wreport .= "<th style='border:1px solid black; text-align:center;'>SGPA</th>
    			  <th style='border:1px solid black; text-align:center;'>CGPA</th>
    			  <th style='border:1px solid black; text-align:left;'>Status</th>";
    		}

    		$cl++;
    		
    	}
    	$wreport .= "<th style='text-align:center; border:1px solid black;'></th>";
    	$wreport .= "</thead><tbody>";
    	$count = 1;
	    $wcount = 0;
	    $semregModel = new \backend\models\SemesterRegistration2Detail();
    	foreach ($gsets as $gset) {

    		$stds = \backend\models\WGroupAssignment::find()
				->where(['gas_gset_relId'=>$gset->id])
				->innerJoin('student','student.id = w_group_assignment.gas_std_relId')
				->innerJoin('user','user.id = student.std_user_relId')
				->orderBy('user.first_name')
				->all();
    		$cGset = NULL;

    		if ($stds && count($stds) > 0) {
    			foreach ($stds as $std) {
    				$student = $std->gasStdRel;
    				$stdRow = "";
    				$showRow = true;
    				if ($student) {
    					$cGset = $std->gasGsetRel;
    				}else{
    					continue;
    				}
    				$stdRow .= "<tr onclick='adClas({$count})' id='row_{$count}'>";
    				$stdRow .= "<td style='border:1px solid black;'>".$count."</td>";
    				$stdRow .= "<td style='border:1px solid black; text-align:center;'>{$student->std_regId}</td>
    					<td style='border:1px solid black; text-align:left;'>{$student->getFullName()} <b>({$student->stdDeptRel->dept_code})</b></td>";
    				$c = $initialClassLevel->clslvl_num;
    				while ($c <= $clslvlObj->clslvl_num) {

    					$semGrade1 = getSemGrade($student->id,$c,"I",$cGset, $progId);
    					// Y-I
    					$set1 = isset($semGrade1[3])?'bold':'normal';
		        		$stdRow .= "<td style='border:1px solid black; text-align:center; font-weight:{$set1};'>$semGrade1[0]</td>";
		        		$stdRow .= "<td style='border:1px solid black; text-align:center; font-weight:{$set1};'>$semGrade1[1]</td>";
		        		$stdRow .= "<td style='border:1px solid black; text-align:left; font-weight:{$set1};'>$semGrade1[2]</td>";

		        		// Y-II
		        		$semGrade2 = getSemGrade($student->id,$c,"II",$cGset, $progId);
		        		$set2 = isset($semGrade2[3])?'bold':'normal';
		        		$stdRow .= "<td style='border:1px solid black; text-align:center; font-weight:{$set2};'>$semGrade2[0]</td>";
		        		$stdRow .= "<td style='border:1px solid black; text-align:center; font-weight:{$set2};'>$semGrade2[1]</td>";
		        		$stdRow .= "<td style='border:1px solid black; text-align:left; font-weight:{$set2};'>$semGrade2[2]</td>";

		        		if ($ext) {
		        		// Y-III (Summer)
		        		$set3 = isset($semGrade3[3])?'bold':'normal';
		        		$semGrade3 = getSemGrade($student->id,$c,"Summer",$cGset, $progId);
		        		$stdRow .= "<td style='border:1px solid black; text-align:center; font-weight:{$set3};'>$semGrade3[0]</td>";
		        		$stdRow .= "<td style='border:1px solid black; text-align:center; font-weight:{$set3};'>$semGrade3[1]</td>";
		        		$stdRow .= "<td style='border:1px solid black; text-align:left; font-weight:{$set3};'>$semGrade3[2]</td>";
		        		}

		        		// $showRow = (isset($semGrade3[3]) || isset($semGrade2[3]) || isset($semGrade1[3]))?
		        			// ():();
		        		$showRow = isset($semGrade3[3])?(($semGrade3[4] == 2||$semGrade3[4] == 3 || $semGrade3[4] == 0)?true:false):
		        			(isset($semGrade2[3])?(($semGrade2[4] == 2 || $semGrade2[4] == 3 || $semGrade2[4] == 0)?true:false):
		        				(isset($semGrade1[3])?(($semGrade1[4] == 2 || $semGrade1[4] == 3 || $semGrade1[4] == 0)?true:false):
	        						false));
		        		$c++;
		        	}
		        	$fNgs = $semregModel->getNgFs($student,$cGset);
		        	$stdRow .= "<td style='border:1px solid black; text-align:left;'>{$fNgs}</td>";
    				$stdRow .= "</tr>";
    			if($showRow){
				$count++;
    				$wreport .= $stdRow;
    			}
    			}
    		}
    	}
    	$wreport .= "</tbody></table>";
        echo $wreport;
    }else{
    	echo "No Result.";
    }


    function getSemGrade($stdId,$clslvl,$sem,$cGset,$program = NULL){
    	$semObject = \backend\models\Semester::findOne(['semester_name'=>$sem]);
    	$clslvlObj = \backend\models\ClassLevel::findOne(['clslvl_num'=>$clslvl,'clslvl_program_relId'=>$program]);
    	$stdObject = \backend\models\Student::findOne($stdId);

    	$semregObj = new \frontend\models\SemesterRegistration2Detail();
    	$semReport = $semregObj->getSemregRecord($stdId,$clslvlObj->id,$semObject->id,$cGset);

    	if (is_array( $semReport )) {
    		$semReport[0] = is_numeric($semReport[0])?sprintf("%0.2f",$semReport[0]):$semReport[0];
    		$semReport[1] = is_numeric($semReport[1])?sprintf("%0.2f",$semReport[1]):$semReport[1];
    		return $semReport;
    	}
    	return [NULL,NULL,NULL];
    }
 ?>


