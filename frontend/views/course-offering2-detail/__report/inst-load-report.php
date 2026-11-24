
<?php 

if (isset($model) && !empty($model)) {
	$coffDets = $model;
	if ($coffDets && count($coffDets) > 0) {
		// foreach($coffDets as $coffDet){
		// 	if ($coffDet->coff2det_acProg_relId) {
		// 		echo "<p>{$coffDet->coff2detAcProgRel->getClassLevelHeader()}</p>";
		// 	}
		// }
		$count = 0;
		$iTBA = 0;
		$taTBA = 0;
	?>
	<table class='table table-condensed table-bordered table-sm'>
		<caption style="font-weight: normal; color: #000; text-align: center;">
			Academic staff load report of academic year <?= $coffDets[0]->coff2detAcProgRel->getAcadYearHeader() ?>
		</caption>
		<thead style="font-weight: bold;">
			<th>#</th>
			<th>Staff Name</th>
			<th>Department</th>
			<th>Academic Rank</th>
			<th colspan="3" style="text-align: center;">Course Oferrings</th>
			<th>Total</th>
			<th>Remark</th>
		</thead>
		<tbody>
			<?php  $instGroups = "";
			foreach ($coffDets as $coffDet){
					if (!$coffDet->coff2det_inst_relId) {
						$iTBA += $coffDet->coff2detCourseRel->course_crdthr;
						continue;
					}

					if (!$coffDet->coff2det_TA_relId && $coffDet->coff2detCourseRel->course_lab_hour != 0 && $coffDet->coff2detCocatRel->cocat_courseProject != 0) {
						$taTBA += $coffDet->coff2detCourseRel->course_lab_hour;
					}

					$offerings = $coffDet->getOfferedGroups();
					$cc = count($offerings);
					if ($cc > 0) {
						$offering = $offerings[0];
			            $gset = $offering->coff2detCoff2Rel->coff2GroupRel;
			            $enrolledStudents = $offering->coff2detCoff2Rel->getSemesterRegistration2Details($offering->coff2det_course_relId)->count();
			            $instr = $coffDet->coff2det_inst_relId ? $coffDet->coff2detInstRel->getFullName() : "TBA";
			            $dept = $coffDet->coff2det_inst_relId ? $coffDet->coff2detInstRel->instDepRel->dept_name : "-";
			            $tar = $coffDet->coff2det_inst_relId ? $coffDet->coff2detInstRel->getAcrRankDisp() : "-";
			            $instGroups .= "<tr>
							<td rowspan={$cc}>".++$count."</td>
							<td rowspan={$cc}>". $instr ."</td>
							<td rowspan={$cc}>". $dept ."</td>
							<td rowspan={$cc}>".$tar."</td>
							<td>{$offering->coff2detCourseRel->getCourseCredits()}</td>
							<td>{$gset->getGsDisp()}</td>
							<td><b>{$gset->gsGroupRel->group_name}</b>:($enrolledStudents)</td>
							<td rowspan={$cc}>-</td>
							<td rowspan={$cc}>-</td>";
					}
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
			            	
			            	</tr>";
			        }
			        $instGroups .= "</tr>";
			    }
				$instGroups .= "<tr><td colspan=6 style='text-align: center;'>TBA(Lecture Hour) : {$iTBA}, TBA(Lab Hour): {$taTBA}</td></tr>" ?>
			<?= $instGroups ?>
		</tbody>
	</table>

	<?php }else{
		echo "<p class='bg-warning'>No record!</p>";
		return;
	}
}else{
	echo "<p class='bg-warning'>No record!</p>";
	return;
}


?>
