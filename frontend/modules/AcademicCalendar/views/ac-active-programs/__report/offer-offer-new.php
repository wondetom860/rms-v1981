<?php 

if (isset($modelActiveProgram) && !empty($modelActiveProgram)) {
	echo "<div class='container-fluid'>";
	if (isset($cocatModels) && count($cocatModels) > 0) {
		echo "<div class='row' style='max-height: 400px; overflow: auto;'>";
		echo "<table class='table table-striped table-condensed table-bordered'>";
		echo "<caption style='font-weight: normal; font-color:#000; font-size: 10pt; '>Offer courses for academic program : <i>{$modelActiveProgram->getClassLevelHeader()}-[{$modelActiveProgram->academicYearRel->getAcHeader()}]</i></caption>";
		echo "<thead>
				<th>#</th>
				<th>Course Code</th>
				<th>Course Title</th>
				<th>Course Dept.</th>
				<th>Catalog Set for<br><span style='font-size:7pt;font-style:italic'>Year/Sem.</span></th>
				<th>Remark </th>
				<th>Option</th>
			</thead>";
		echo "<tbody>";
		$count = 0;
		foreach ($cocatModels as $cocatModel) {
			$course = $cocatModel->cocatCourseRel;
			$Remark = $cocatModel->cocat_courseelectives ? 'Elective' : ($cocatModel->cocat_courseProject ? 'Project Course' : ($cocatModel->cocat_courseMajor?'Major' : 'Common'));
			$catSet = $cocatModel->getCatalogSet();
			$cc = $modelActiveProgram->checkIfOffered($cocatModel->id);
			$option = $cc ? 'Offered' : "<center><input title='Offer this course' type='checkBox' onchange='addToOfferList({$cocatModel->id})' id='{$cocatModel->id}_000'/></center>";
			echo "<tr>
					<td>".++$count."</td>
					<td>{$course->getCourseCredits()}</td>
					<td>{$course->course_title}</td>
					<td>{$course->courseDeptRel->dept_code}</td>
					<td>{$catSet}</td>
					<td>{$Remark}</td>
					<td title='{$cc}'>{$option}</td>
			</tr>";
		}
		$offerButton = "<button class='btn btn-info btn-xs pull-right' title='Offer selcted course(s)' onclick='offerCourse();return false;'>Offer Course(s)</button>";
		echo "<tr></tr>
			<tr><td colspan=7></td></tr>";
		echo "</tbody>";
		echo "</table>";
		echo "</div><div class='row' style='background-color: #FED;'>";
		echo "<span style='font-size:8pt' id='selected_course_count'></span> {$offerButton}";
		echo "</div>";
	}else{
		echo "<p class='class-'bg-danger'>Course Catalog entry not found.</p>";
	}
	echo "</div>";
}else{
	echo "<p class='class-'bg-danger'>Invalid access, Active academic program not know.</p>";
}

?>

<script type="text/javascript">
	window.addToOfferList = (catId) => {
		status = addtoList(Number(catId));
        $('#'+catId+'_000').prop({'checked':(status == 1)});
        $("#selected_course_count").text('Courses Selected : '+__offerList.length);
	}

	window.addtoList=function(catId){
        r = 0;
        if(jQuery.inArray(catId,__offerList) !== -1){ 
        	// if item is already selected, remove it because user deselected it.
            __offerList.splice($.inArray(catId,__offerList),1);
            r= 0;
        }else{//going to add the item to the list.
            __offerList.push(catId);
            r= 1;
        }
        return r;
    }
</script>