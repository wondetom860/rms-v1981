<?php if (isset($model) && count($model) > 0): ?>
	<table class="table table-condensed table-bordered table-sm" cellspacing="0" cellpadding="2">
		<caption style="font-weight: bold; color: #000;">
			<center>
				Student Full Name : <u><?= $model->semregStdRel->getFullName() ?></u><br>
				ID NO. : <u><?= $model->semregStdRel->std_regId ?></u><br>
				Program : <u><?= $model->semregStdRel->getProgram() ?></u><br>
				Department : <u><?= $model->semregStdRel->getDepartment() ?></u><br>
				Semester registratin Entry Detail :<u> <?= $model->semregAcadYearRel->sch_year_desc."-".$model->semregSemesterRel->semester_name ?></u>
				<hr>
			</center>
		</caption>
		<tr>
			<td>Courses Enrolled:</td>
			<td><?= $this->render('enrollment-detail/enrolled-courses',['srd2s' => $model->semesterRegistration2Details]) ?></td>
		</tr>
		<tr>
			<td>Continous Assessment Entry:</td>
			<td><?= $this->render('enrollment-detail/continuous-assessment-entries',['srd2s' => $model->semesterRegistration2Details]) ?></td>
		</tr>
		<tr>
			<td>Course Re-assessment Entry:</td>
			<td><?= $this->render('enrollment-detail/reassessment-entry',['srd2s' => $model->semesterRegistration2Details]) ?></td>
		</tr>
		<tr>
			<td>Supplimentary Exam Request:</td>
			<td><?= $this->render('enrollment-detail/supplimentary-exam',['srd2s' => $model->semesterRegistration2Details]) ?></td>
		</tr>
		<tr>
			<td>Make-up Exam request:</td>
			<td><?= $this->render('enrollment-detail/make-up-exam-entry',['srd2s' => $model->semesterRegistration2Details]) ?></td>
		</tr>
		<tr>
			<td>Course-Add Entry:</td>
			<td><?= $this->render('enrollment-detail/course-add-entry',['srd2s' => $model->semesterRegistration2Details]) ?></td>
		</tr>
		<tr>
			<td>Course-Drop Entry:</td>
			<td><?= $this->render('enrollment-detail/course-drop-entry',['srd2s' => $model->semesterRegistration2Details]) ?></td>
		</tr>
		<tr class="bg-warning">
			<td class="bg-warning" colspan="2">
				<center>
					<button class="btn btn-warning btn-sm" onclick="removeEntryLast(<?= $model->id ?>);" title='Last approval for removing these entries...'>Confirm Remove</button>
				</center>
			</td>
		</tr>
		
	</table>
<?php endif ?>

<script type="text/javascript">
	window.removeEntryLast = (semregId) => {
		$.post('',{semregId:semregId}).done((resp) => { if (resp == 1) { $('#modal').modal('hide'); }  })
	}
</script>