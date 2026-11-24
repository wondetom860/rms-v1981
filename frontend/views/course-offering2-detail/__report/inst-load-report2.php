<div id="reportContainer">
<?php 
if (isset($model) && !empty($model)) {
	$coffDets = $model;
	// $staffModel
	// $activeProgram
	if ($coffDets) {
		if ($activeProgram ) {
			// code...
		}else{
			echo "No record found for the selected academic year";
			return;
		}
		$count = 0;
		$TBACredits = $coffDets->getTBACredits($activeProgram->id,'INSTRUCTOR');
	?>
	<table class='table table-condensed table-bordered table-sm table-striped'>
		<caption style="font-weight: normal; color: #000; text-align: center; font-size: 1.2em;">
			<b>Academic staff load report of ACY: </b> <u><?= $activeProgram->getAcHeader() ?></u>
		</caption>
		<thead style="font-weight: bold;">
			<th>#</th>
			<th>Staff Name</th>
			<th>Department</th>
			<th>Academic Rank</th>
			<th colspan="4" style="text-align: center;">Course Oferrings</th>
			<th>Total</th>
			<th>Remark</th>
		</thead>
		<tbody>
			<?php  $instGroups = "";
			foreach ($staffModel as $staff){
				$__rowSPanCOunt = 0;
				$coff2detInst = $model->getStaffCourseOffering('INSTRUCTOR',$staff->id,$activeProgram->id);
				if ($coff2detInst && count($coff2detInst) > 0) {
					$tad = false;
					$__rowSPanCOunt += count($coff2detInst);
					$role = "Instructor.";
					$offering = $coff2detInst[0];
		            $gset = $offering->coff2detCoff2Rel->coff2GroupRel;
		            $enrolledStudents = $offering->coff2detCoff2Rel->getSemesterRegistration2Details($offering->coff2det_course_relId)->count();
					$instr = $staff->getFullName();
			        $dept = $staff->instDepRel->dept_name;
			        $tar = $staff->getAcrRankDisp();

			        $coff2detTA = $model->getStaffCourseOffering('TA',$staff->id,$activeProgram->id);
			        if ($coff2detTA && count($coff2detTA) > 0) {
			        	$tad = true;
			        	$__rowSPanCOunt += count($coff2detTA);
			        }
			        $offerings = $coff2detInst;
			        $cc = $__rowSPanCOunt;
					$instGroups .= "<tr>
						<td rowspan={$cc} style='vertical-align: middle;'>".++$count."</td>
						<td rowspan={$cc} style='vertical-align: middle;'>". $instr ."</td>
						<td rowspan={$cc} style='vertical-align: middle;'>". $dept ."</td>
						<td rowspan={$cc} style='vertical-align: middle;'>".$tar."</td>
						<td>{$offering->coff2detCourseRel->getCourseCredits()}</td>
						<td>{$gset->getGsDisp()}</td>
						<td><b>{$gset->gsGroupRel->group_name}</b>:($enrolledStudents)</td>
						<td>{$role}</td>
						<td rowspan={$cc}  style='vertical-align: middle;'>-</td>
						<td rowspan={$cc}  style='vertical-align: middle;'>-</td>";
					$cb = 0;
			        foreach ($offerings as $offering) {
			        	if ($cb == 0) {
			        		$cb++;
			        		continue;
			        	}
			            $enrolledStudents = $offering->coff2detCoff2Rel->getSemesterRegistration2Details($offering->coff2det_course_relId)->count();
			            $gset = $offering->coff2detCoff2Rel->coff2GroupRel;
			            $instGroups .= "<tr>
			            	
			            	<td>{$offering->coff2detCourseRel->getCourseCredits()}</td>
			            	<td>{$gset->getGsDisp()}</td>
							<td><b>{$gset->gsGroupRel->group_name}</b>:($enrolledStudents)</td>
			            	<td>{$role}</td>
			            	</tr>";
			        }

			        // in case if the instructor holds both lecture and lab hour of the offering...
			        if ($tad) {
			        	$role = 'Techn. Asst.';
				        $offerings = $coff2detTA;
				        foreach ($offerings as $offering) {
				            $enrolledStudents = $offering->coff2detCoff2Rel->getSemesterRegistration2Details($offering->coff2det_course_relId)->count();
				            $gset = $offering->coff2detCoff2Rel->coff2GroupRel;
				            $instGroups .= "<tr>
				            	
				            	<td>{$offering->coff2detCourseRel->getCourseCredits()}</td>
				            	<td>{$gset->getGsDisp()}</td>
								<td><b>{$gset->gsGroupRel->group_name}</b>:($enrolledStudents)</td>
				            	<td>{$role}</td>
				            	</tr>";
				        }
			        }
				}else{
					$coff2detTA = $model->getStaffCourseOffering('TA',$staff->id,$activeProgram->id);
					if ($coff2detTA && count($coff2detTA) > 0) {
						$role = "Techn. Asst.";

						$offering = $coff2detTA[0];
		            	$gset = $offering->coff2detCoff2Rel->coff2GroupRel;
						// $offerings = $coff2detTA;
						$instr = $staff->getFullName();
				        $dept = $staff->instDepRel->dept_name;
				        $tar = $staff->getAcrRankDisp();

				        $cc = count($coff2detTA);

						$instGroups .= "<tr>
							<td rowspan={$cc}  style='vertical-align: middle;'>".++$count."</td>
							<td rowspan={$cc}  style='vertical-align: middle;'>". $instr ."</td>
							<td rowspan={$cc}  style='vertical-align: middle;'>". $dept ."</td>
							<td rowspan={$cc}  style='vertical-align: middle;'>".$tar."</td>
							<td>{$offering->coff2detCourseRel->getCourseCredits()}</td>
							<td>{$gset->getGsDisp()}</td>
							<td><b>{$gset->gsGroupRel->group_name}</b></td>
							<td>{$role}</td>
							<td rowspan={$cc}  style='vertical-align: middle;'>-</td>
							<td rowspan={$cc}  style='vertical-align: middle;'>-</td>";
						$offerings = $coff2detTA;
						$cb = 0;
				        foreach ($offerings as $offering) {
				        	if ($cb == 0) {
				        		$cb++;
				        		continue;
				        	}
				            $enrolledStudents = $offering->coff2detCoff2Rel->getSemesterRegistration2Details($offering->coff2det_course_relId)->count();
				            $gset = $offering->coff2detCoff2Rel->coff2GroupRel;
				            $instGroups .= "<tr>
				            	
				            	<td>{$offering->coff2detCourseRel->getCourseCredits()}</td>
				            	<td>{$gset->getGsDisp()}</td>
								<td><b>{$gset->gsGroupRel->group_name}</b>:($enrolledStudents)</td>
				            	<td>{$role}</td>
				            	</tr>";
				        }
					}else{
						continue;
					}
				}					
			    $instGroups .= "</tr>";
			    }
				$instGroups .= "<tr style='background-color: #9DE;'><td colspan=8 style='text-align: center;'>TBA(Lecture Hour) : {$TBACredits['iTBA']}, TBA(Lab Hour): {$TBACredits['tTBA']}</td></tr>" ?>
			<?= $instGroups ?>
		</tbody>
	</table>

	<?php }else{
		echo "<p class='bg-warning'>No record!</p>";
		return;
	}
}else{
	if (isset($selectYear) && !empty($selectYear)) {
		// display academic year selection label
		$options = "<option value=''>--Select academic year--</option>";
		$models = \frontend\modules\AcademicCalendar\models\AcAcademicYear::find()
			->orderBy([
				'school_year' => SORT_DESC,
				'semester' => SORT_DESC,
			])
			->all();
		foreach ($models as $acYear) {
			$options .= "<option value='{$acYear->id}'>{$acYear->getAcHeader()}</option>";
		}
		echo "<div>
				<select class='form-control' onchange='getReport($(this).val());return false;'>
					{$options}
				</select>
			</div>";
	}else{
		echo "<p class='bg-warning'>No record!</p>";
		return;
	}
	
}

?>
</div>
<script type="text/javascript">
	window.getReport = (acadYearId) => {
		$("#reportContainer").text('>>>Generating report...');
		$.get('/course-offering2-detail/get-inst-load-report-print2?gadget=Typical&acadYearId='+acadYearId).done((rep) => {
			$("#reportContainer").html(rep);
		});
	}
</script>