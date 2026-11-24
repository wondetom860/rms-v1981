<?php

if (isset($coff2detModel) && !empty($coff2detModel)) {
	// var_dump($model);return;
	$canManageContAss = false;
	$btnCreateCategory = "";
	if ($coff2detModel->getCatMaxPointSum() < 100) {
		if ($coff2detModel->checkRequired2($coff2detModel->coff2det_course_relId)) {
			$CourseCordinator = \backend\models\CourseCordinator::getCordinator($coff2detModel->coff2det_course_relId, $coff2detModel->coff2detCoff2Rel->coff2AcadYearRel->id, $coff2detModel->coff2detCoff2Rel->coff2SemRel->id);
			if ($CourseCordinator && ($CourseCordinator->ccInstRel->inst_user_relId == Yii::$app->user->identity->id)) {
				$btnCreateCategory = "<button class='btn btn-xs btn-primary pull-right' onclick='CreateCategory({$coff2detModel->id})'>Add Category</button>";
				// echo "<p class='bg-warning'>{$btnCreateCategory}</p>";
				$canManageContAss = true;
			} else {
				$btnCreateCategory =  "<small class='bg-warning'>Course assessment plan record not found. Please wait for the course coordinator to setup it.</small>";
			}
		} else {
			$btnCreateCategory = "<button class='btn btn-xs btn-primary pull-right' onclick='CreateCategory({$coff2detModel->id})'>Add</button>";
			// echo "<p class='bg-warning'>{$btnCreateCategory}</p>";
			$canManageContAss = true;
		}
	}
	$coff2 = $coff2detModel->coff2detCoff2Rel;
	echo "<table class='table table-condensed table-sm table-bordered table-hover'>";
	echo "<caption style='color: #000; text-align: center; font-weight: normal'>
			<h4>Course Assessment Category {$btnCreateCategory}</h4>
			<b>ACY: </b><u>{$coff2->coff2AcadYearRel->sch_year_desc}-{$coff2->coff2SemRel->semester_name}</u>&nbsp;&nbsp;&nbsp;
			<b>Class Year: </b><u>{$coff2->coff2GroupRel->gsClslvlRel->getClslvlDisp()}</u><br>
			<b>Course: </b><u>{$coff2detModel->coff2detCourseRel->getCourseCredits()}</u><br>
	</caption>";
	echo "<thead style='background-color: #FD8;'>
			<td>#</td>
			<td>Category Name</td>
			<td>Max. Point</td>
			<td>Instances</td>
			<td>Weight</td>
			<td class='noprint'>Options</td>
	</thead><tbody>";
	$count = 0;
	$total_category = 0;
	$total_instances = 0;
	$unapproved = 0;
	foreach ($model as $assCategory) {
		// $assCategory
		$instances = get_assessment_instances($assCategory);
		$countts = $instances[1];
		$instance = $instances[0];
		$total_category += $assCategory->cat_maxPoint;
		$total_instances += $instances[2];
		$addInstanceBtn = "";
		$btnCategUpdate = "";
		$btnCategDelete = "";
		$approved = $instances[3];
		if($approved == 0){
			$unapproved++;
			if (($assCategory->cat_maxPoint > $assCategory->instantaitedPoint())) { //indicates CA is not approved
				// $unapproved++;
				$btnCategUpdate = "<button class='btn btn-xs btn-info pull-right' onclick='updateCategory({$assCategory->id}); return false;'>Edit</button>";
				$btnCategDelete = "<button class='btn btn-xs btn-danger pull-right' onclick='deleteCategory({$assCategory->id}); return false;'>x</button>";
			}
			if ($instances[2] < $assCategory->cat_maxPoint) {
				// add create instance option
				$addInstanceBtn = "<button class='btn btn-xs pull-right btn-primary' onclick='addInsatnce({$assCategory->id});return false;' title='Add instance of this category'>+</button>";
			}
		}
		echo "<tr>
				<td rowspan={$countts} style='vertical-align: middle'>" . ++$count . "</td>
				<td rowspan={$countts} style='vertical-align: middle'>{$assCategory->catCatagoryRel->catagory_name}{$btnCategUpdate}{$btnCategDelete}</td>
				<td rowspan={$countts} style='vertical-align: middle; text-align: center;'>{$assCategory->cat_maxPoint}{$addInstanceBtn}</td>
				{$instance}";
	}
	echo "<tr style='font-weight: bold; text-align:center;'>
			<td colspan=2>Total</td>
			<td>{$total_category}</td>
			<td></td>
			<td style='text-align: center;'>{$total_instances}</td>
	</tr>";
	echo "</tbody></table>";
	if ($total_category == 100 && $total_instances == 100) {
		$approval = $unapproved == 0 ? "Approved" : "Pending";
		echo "<p class='bg-success'>All categories are setup correctly. Approval Status: <u>{$approval}</u></p>";
	} else {
		echo "<p class='bg-warning'>Total category weight is not equal to 100%.</p>";
	}
} else {
	// check if course coordinator
	echo "Invalid access";
	return;
}


function get_assessment_instances($assCategory)
{
	$instances = $assCategory->getInstanceMarkAssignments()->orderBy('ima_instanceName')->all();
	$inst = "";
	$count = 1;
	$i_total = 0;
	$approved = 0;
	if ($instances && count($instances) > 0) {
		foreach ($instances as $instance) {
			$btnDel = '';
			$btnUpdt = '';
			if ($instance->ima_approved == 1) {
				$approved++;
			} else {
				$btnDel = "<button class='btn btn-xs btn-danger' onclick='deleteInstance({$instance->id}); return false;'>x</button>";
				$btnUpdt = "<button class='btn btn-xs btn-info' onclick='updateInstance({$instance->id}); return false;'>Edit</button>";
			}
			$ima = sprintf("%0.2f", $instance->ima_possiblePoint);
			$inst .= "</tr><tr>
				<td>{$instance->ima_instanceName}</td>
				<td style='text-align: center;'>{$ima}</td>
				<td  class='noprint' style='text-align: center;'>{$btnDel}{$btnUpdt}</td>
			</tr>";
			$count++;
			$i_total += $instance->ima_possiblePoint;
		}
	} else {
		// check if course coordinator, if he/she is, then allow creating instances from category
		$count = 2;
		$inst = "</tr><tr><td colspan=2>Instance NOT found.</td></tr>";
	}

	return [$inst, $count, $i_total, $approved];
}

?>

<script>
	window.deleteCategory = (id) => {
		if (confirm('Are you sure you want to delete this Category?')) {
			$.post('/CourseAssessment/cont-assess-catagory/delete?id=' + id).done(function(data) {
				if (data == 1) {
					// alert('Instance deleted successfully.');
					showEvalutorsList(coff_det_id);
				} else {
					alert('Failed to delete instance.');
				}
			});
		}
	}
	window.updateCategory = (id) => {
		$('#modal').find('.modal-header').html('Update Assessment Category');
		$('#modalContent').css({
			'text-align': 'left',
			'margin-top': '0px'
		});
		$('#modalContent').html('Loading form...');
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
		$('#modal').modal('show');
		$.get('/CourseAssessment/cont-assess-catagory/update?id=' + id).done(function(data) {
			$("#modalContent").html(data);
		});
	}


	window.deleteInstance = (id) => {
		if (confirm('Are you sure you want to delete this instance?')) {
			$.post('/CourseAssessment/instance-mark-assignment/delete?id=' + id).done(function(data) {
				if (data == 1) {
					// alert('Instance deleted successfully.');
					showEvalutorsList(coff_det_id);
				} else {
					alert('Failed to delete instance.');
				}
			});
		}
	}
	window.updateInstance = (id) => {
		$('#modal').find('.modal-header').html('Update Assessment Instance');
		$('#modalContent').css({
			'text-align': 'left',
			'margin-top': '0px'
		});
		$('#modalContent').html('Loading form...');
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
		$('#modal').modal('show');
		$.get('/CourseAssessment/instance-mark-assignment/update?id=' + id).done(function(data) {
			$("#modalContent").html(data);
		});
	}
	__category_id = 0;
	window.addInsatnce = (category_id) => {
		// create instance
		__category_id = category_id;
		$('#modal').find('.modal-header').html('Create Assessment Instance');
		$('#modalContent').css({
			'text-align': 'left',
			'margin-top': '0px'
		});
		$('#modalContent').html('Loading form...');
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
		$('#modal').modal('show');
		$.get('/CourseAssessment/instance-mark-assignment/create', {
			'categoryId': category_id
		}).done(function(data) {
			$("#modalContent").html(data);
		});
	}
	window.CreateCategory = (coff_det_id) => {
		// alert(coff_det_id);
		$('#modal').find('.modal-header').html('Add Assessment Category');
		$('#modalContent').css({
			'text-align': 'left',
			'margin-top': '0px'
		});
		$('#modalContent').html('Loading form...');
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
		$('#modal').modal('show');
		$.get('/CourseAssessment/cont-assess-catagory/create', {
			coff2det_id: coff_det_id
		}).done(function(data) {
			$('#modalContent').html(data);
		});
	}
</script>