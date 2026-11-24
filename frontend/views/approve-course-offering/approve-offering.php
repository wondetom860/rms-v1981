<?php

$this->title = "Approve Course Offering";

?>
<h2>Course Offerings record (Approval Pending)</h2>

<div class="container-fluid" style="max-height:540px; overflow-x: auto;">
	<?php
	if (!is_null($unApprovedOfferings)) {
		?>
		<table class="table table-hover table-striped">
			<thead>
				<th>#</th>
				<th>Acad. Year</th>
				<th>Program</th>
				<th>Dept.</th>
				<th>Yr.</th>
				<th>Courses</th>
				<th>Option</th>
			</thead>
			<tbody>
		<?php
		$count = 0;
		foreach ($unApprovedOfferings as $Coffkey) {
			?>
			<tr>
				<td><?=  ++$count ?></td>
				<td><?= $Coffkey->cotAcadYearRel->sch_year_desc .'-'. $Coffkey->cotSemRel->semester_name  ?></td>
				<td>
					<b>[<?=  $Coffkey->cotAdmRel->admission_name .' '.$Coffkey->cotProgRel->program_name ?>]</b>
				</td>
				<td><?=  $Coffkey->cotDeptRel->dept_name ?></td>
				<td><?= $Coffkey->cotClslvelRel->clslvl_num ?></td>
				<td>
					<div class="container-fluid">
						<button class="btn btn-secondary btn-xs pull-right" onclick="toggleDisplay(<?= $Coffkey->id ?>)">[:-:]</button>
					</div>
					<div style="display: none;" class="container-fluid"  id="div_<?= $Coffkey->id ?>" >
						<?php
							if (is_null($Coffkey->cot_cocat_IDS)) {
								echo "<span class='bg-error'>List Empty.</span>";
							}else{
								$cocatIds = json_decode($Coffkey->cot_cocat_IDS);
								if (is_array($cocatIds) && count($cocatIds) > 0) {
									echo getCocatIds($cocatIds);
								}else{
									echo "<span class='bg-error'>List Empty.</span>";
								}
							}
						?>
					</div>
				</td>
				<td>
					<button class="btn btn-primary btn-xs" title="Show/Hide Course List" onclick="approveOffering(<?= $Coffkey->id ?>)">Approve</button>
					<span class="bg-error" id="error_<?= $Coffkey->id ?>"></span>
				</td>
			</tr>
			<?php
		}
		?>
		</tbody>
		</table>
		<?php
	}

	function getCocatIds($cocatIds){
		$table = "";
	    if ($cocatIds && count($cocatIds) > 0) {
	        $crsum2 = 0;
	        $table .= "<table class='table table-condensed table-bordered table-hover' style='font-size:7pt;'>";
	        $table .= "<thead style='background-color:#8D0;'>
	            <th style='border:1px solid black;'>#</th>
	            <th style='border:1px solid black;'>Course Code</th>
	            <th style='border:1px solid black;'>Corse Title</th>
	            <th style='border:1px solid black;'>Crdt. Hr.</th>";
	        $table .= "</thead><tbody>";
	            $count = 0;
	       foreach ($cocatIds as $key2) {
	       		$cocat = \backend\models\CourseCatalog::findOne($key2);
	       		if ($cocat) {
	       			$c = $cocat->cocatCourseRel;
		            $crsum2 += $c->course_crdthr;
		            $table .= "<tr>
		                <td style='border:1px solid black;'>".++$count."</td>
		                <td style='border:1px solid black;'>{$c->course_code}</td>
		                <td style='border:1px solid black;'>{$c->course_title}</td>
		                <td style='font-weight:bold; text-align:center; border:1px solid black;'>{$c->course_crdthr}</td>
		                </tr>
		                ";
	       		}else{
	       			$table .= "<tr><td colspan = 4><span class='bg-error'>Course Error</span></td></tr>";
	       		}
	       }
	       $table .= "<tr style='font-weight:bold; text-align:center;'>
	                <td colspan = 3  style='border:1px solid black;'>Total Credit</td>
	                <td style='text-decoration:underline; border:1px solid black;'>{$crsum2}</td>
	            </tr>";
	        $table .= "</tbody></table>";
	    }else{
	        $table = "No Course Found.";
	    }
	    return $table;
	}
	?>
</div>

<script type="text/javascript">

	window.toggleDisplay = function(id){
		$('#div_'+id).toggle('show');
	}
	window.approveOffering = function(id){
		if (confirm('Continue Operation?')) {
			$.post('/approve-course-offering/approve-offering',{id:id}).done(
				function(data){
				 if (data == 1) {
				 	window.location.href = window.location;
				 }else{
				 	$('#error_'+id).html(data);
				 }
			});
		}
	}
</script>