<?php 


	if (isset($coff2s)) {
		echo "<br><table class='table table-striped table-bordered table-hover table-sm'>";
		echo "<caption style='font-weight: normal; color:black; text-align:center; background-color: #FD0;'>Courses Offered - {$coff2s[0]->coff2GroupRel->gsClslvlRel->getClslvlDisp()}</caption>";
		echo "<thead>
				<th>#</th>
				<th>Academic Year</th>
				<th>Group</th>
				<th>Option</th>
			</thead>";
		$count = 0;
	    foreach ($coff2s as $coff2) {
	    	$btn = "<a class='btn btn-xs btn-warning pull-right' onclick='showOfferingDetail({$coff2->id});return false;'>Show</a>";
	        echo "<tr>
	        		<td>".++$count."</td>
	        		<td>{$coff2->coff2detAcProgRel->getAcadYearHeader()}</td>
	        		<td>{$coff2->coff2GroupRel->getGsDisp()}|({$coff2->coff2GroupRel->gsGroupRel->group_name})</td>
	        		<td>{$btn}</td>
	        	</tr>"	;
	        echo "<tr class='__tr__'><td></td><td colspan='3' id='____{$coff2->id}____'></td></tr>";
	    }
	    echo "</table>";
	}

 ?>

 <script type="text/javascript">
 	window.showOfferingDetail = (coff2Id) => {
 		$(".__tr__").css({'display':'none'});
 		$("#____"+coff2Id+"____").parent().toggle('display');
 		$("#____"+coff2Id+"____").html("Loading ... ");

 		$.post('/AcademicCalendar/ac-active-programs/get-offering-detail',{coff2Id:coff2Id}).done((resp) => { $("#____"+coff2Id+"____").html(resp); });
 	}
 </script>