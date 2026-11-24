<?php

use yii\helpers\Html;

$modelGas = $modelStudent
	->getWGroupAssignments()
	->innerJoinWith(['gasGsetRel'])
	->orderBy([
		'gs_acadYear_relId' => SORT_ASC,
		'gs_sem_relId' => SORT_ASC,
	])
	->all();
?>

<div class="row">
	<div class="col-xs-12">
		<h4 class="edusec-border-bottom-warning page-header edusec-profile-title-1">
			<i class="fa fa-grade"></i> <?= Html::encode(Yii::t('stu', 'Group Assignment')) ?>
		</h4>
	</div>
</div>

<div class="row hidden">
	<div class="col-xs-12">
		<h5 class="page-header bg-info"> Group Assignment</h5>
		<!-- <h3 class="hidden"></h3> -->
		<input type="text" class="hidden" id="std_id" value="<?= $modelStudent->id ?>">
	</div><!-- /.col -->
</div>

<div class="container-fluid table-responsive">
	<?php if ($modelGas && count($modelGas) > 0) {
	?>
		<table class="table table-sm table-condensed table-striped table-bordered table-hover" style=" font-size: 8pt;">
			<thead style="background-color: #DFD;">
				<th>#</th>
				<th>Ac. Year</th>
				<th>Dept.</th>
				<th>Group</th>
				<th>Spec.</th>
				<th>Students</th>
				<th>Courses</th>
				<th>Option</th>
			</thead>
			<tbody>
				<?php
				$c = 0;
				$spec = '';
				foreach ($modelGas as $gas) {
					$registerCourse = "";
					$gset = $gas->gasGsetRel;
					$semregId = 0;
					$spec = $gset->gsSpecRel ? $gset->gsSpecRel->spec_name : '';
					$cc = count($gset->wGroupAssignments);
					$courses = $gset->getStudentSemesterRegistrations2($gas->gas_std_relId)
						->all();
					// $semreg = ;
					if ($courses) {
						$semregId = $courses[0]->srd2_semreg_relId;
					}
					$courses = count($courses);
					// ask for course enrollment
					if (Yii::$app->user->can('Admin') || Yii::$app->user->can('Registrar')) {
						if (($courses <= 0)) {
							$registerCourse = "<button class='btn btn-xs btn-warning' title='register course on behalf of this student' onclick='registerForCourse({$gas->gas_std_relId},null);return false;'>Enroll</button>";
						} else {
							$registerCourse = "<span title='Registrar Staff can register for courses on behalf of this student.'><button class='btn btn-xs btn-primary' title='register course on behalf of this student' onclick='registerForCourse({$gas->gas_std_relId},{$semregId},1);return false;'>Enroll+</button></span>";
						}
					}
					$option = $courses <= 0 ?
						"<button class='btn btn-xs btn-danger pull-right' title='Delete this group assignment?' onclick='deleteAssignment({$gas->gas_gset_relId},{$gas->gas_std_relId});return false;'>Delete</button>" :
						"<span title='Course enrolled.'></span>";
				?>
					<!-- <td><?php //$gset->gsClslvlRel->getClslvlDisp() 
								?></td> -->
					<!--	<td><?php //$gset->gsSemRel->semester_name 
								?></td> -->
					<tr>
						<td><?= ++$c ?></td>
						<td><?= $gset->gsAcadYearRel->sch_year_desc ?></td>
						<td><?= $gset->gsDeptRel->dept_code ?></td>
						<td><?= "{$gset->gsGroupRel->group_name}:" . $gset->getGsDisp() ?></td>
						<td><?= $spec ?></td>
						<td style="text-align:center;"><a href="#" onclick="showgAssignments(<?= $gset->id ?>);return false;"><?= $cc ?></a></td>
						<td style="text-align:center;"><a href="#" onclick="showCourses(<?= $semregId ?>);return false;"><?= $courses ?></a></td>
						<td><?= $option . $registerCourse ?></td>
					</tr>
				<?php
				} ?>
			</tbody>
		</table>
	<?php
	} else {
		echo "<p class='bg-warning'> NOT AVAILABLE</p>";
	} ?>
</div>

<script>
	formm = '<div class="panel-body"><div class="container-fluid" id="semRegContainer">Loading your courses...<br><br><br><br><br></div><div class="container-fluid" style="background-color: #ace;">Selected: <span id="courseCount" style="font-style: italic;"></span><br>Total Credit: <span id="crdtSum" style="font-style: italic;"></span>&nbsp;&nbsp;&nbsp;&nbsp; Min. Required Credit: <span id="minCrdt" style="font-style: italic;"></span> Max. Required Credit: <span id="maxCrdt" style="font-style: italic;"></span></div></div><div class="panel-footer"><button class="btn btn-sm btn-success" id="btnEntroll" onclick="enrollCourse();return false;" style="display: none;">Enroll</button></div>';
	const registerForCourse = (stdId, semregId, enrolPlus) => {
		__stdId = stdId;
		courseList = [];
		coffList = [];
		totalCrdtHr = 0;
		__semregId = semregId;
		// $('#btnEntroll').hide();		
		$('#modal').find('.modal-header').html('Register for course');
		$('#modalContent').css('text-align', 'left');
		$('#modalContent').html("<div class='panel panel-default'>" + formm + "</div>");
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
		$('#modal').modal('show');
		$.get('/semester-registration2/getcourses', {
			std: stdId,
			foradd: enrolPlus,
			semregId: semregId
		}).done(function(data) {
			$('#semRegContainer').html(data);
		});
	}
	const showgAssignments = (gsetId) => {
		$('#modal').find('.modal-header').html('Group/Section Assignments');
		$('#modalContent').css('text-align', 'left');
		$('#modalContent').html('Loading data...');
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
		$('#modal').modal('show');
		$.get('/w-group-setup/get-assignments2', {
			wt_gsetUp_entry: gsetId
		}).done(function(data) {
			$('#modalContent').html(data);
		});
	}
	const showCourses = (semreg_id) => {
		__semregId = semreg_id;
		$('#modal').find('.modal-header').html('Course Enrollments');
		$('#modalContent').css('text-align', 'left');
		$('#modalContent').html('Loading data...');
		$('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
		$('#modal').modal('show');
		$.post('/semester-registration/get-srd2-detail', {
			semregId: semreg_id
		}).done(function(data) {
			$('#modalContent').html(data);
		});
	}
	const deleteAssignment = (gset_id, std_id) => {
		// stdId
		// gsetId
		if (confirm('Are you sure to delete this assignment? \n !<THIS OPERATION IS NON-REVERSIBLE>!')) {
			$.post('/w-group-assignment/degroup', {
				gsetId: gset_id,
				stdId: std_id
			}).then((resp) => {
				if (resp == 1) {
					window.location.href = window.location;
				} else {
					alert(resp);
				}
			});
		}
	}

	const enrollCourse = () => {
		// e.preventDefault();
		courseListString = JSON.stringify(courseList); //JSON encode.
		coffListString = JSON.stringify(coffList); //JSON encode.
		// console.log(courseListString);
		// console.log(coffListString);
		// console.log(typeof(courseListString));

		$.post(
			'/semester-registration2/create', {
				coList: courseListString,
				coffList: coffListString,
				stdId: __stdId,
				semregId:__semregId
			},
			function(data) {
				// console.log(data);
				if (data == 1) {
					window.location.href = window.location;
				} else {
					alert(data);
				}
			});
		// console.log('enrolling...');
	};

	courseList = [];
	coffList = [];
	totalCrdtHr = 0;
	__stdId = '';
	__semregId = null;
	window.addEnroll = function(courseId, coffId, crdtHr, maxCredit, minCredit) {

		// Add/remove the Courseid from the Courselist
		if (jQuery.inArray(courseId, courseList) !== -1) { // if item is already selected, remove it because user deselected it.
			courseList.splice($.inArray(courseId, courseList), 1);
			totalCrdtHr -= crdtHr;
		} else { //going to add the item to the list.
			courseList.push(courseId);
			totalCrdtHr += crdtHr;
		}

		// Add/remove the Courseid from the Cofflist
		if (jQuery.inArray(coffId, coffList) !== -1) { // if item is already selected, remove it because user deselected it.
			coffList.splice($.inArray(coffId, coffList), 1);
		} else { //going to add the item to the list.
			coffList.push(coffId);
		}

		$('#courseCount').html(courseList.length);
		$('#btnEntroll').show();
		$('#crdtSum').html(totalCrdtHr);
		$('#minCrdt').html(minCredit);
		$('#maxCrdt').html(maxCredit);
	}
</script>