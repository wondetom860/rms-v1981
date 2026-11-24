<?php
$this->title = 'Student Semester Registration Report';
?>

<style type="text/css">
	.watermark {
		transform: rotate(-40deg);
		white-space: nowrap;
		position: absolute;
		top: 25%;
		left: 6%;
		text-align: center;
		font-size: 2.25em;
		font-style: italic;
		font-weight: bold;
		font-family: 'Courier New', Courier, monospace;
		opacity: 0.1;
		z-index: 99;
	}

	.diagonal-text {
		/* width: 300px; */
		/* height: 300px; */
		border: 1px solid #000;
		/* display: flex; */
		justify-content: center;
		align-items: center;
		overflow: hidden;
		position: relative;
	}

	.diagonal-text span {
		transform: rotate(-52deg);
		white-space: nowrap;
		position: absolute;
		top: 50%;
		left: 40%;
		font-size: 3em;
		font-style: italic;
		font-weight: bold;
		font-family: 'Courier New', Courier, monospace;
		opacity: 0.4;
	}

	.table-bordered>thead>tr>th,
	.table-bordered>tbody>tr>th,
	.table-bordered>tfoot>tr>th,
	.table-bordered>thead>tr>td,
	.table-bordered>tbody>tr>td,
	.table-bordered>tfoot>tr>td {
		border-collapse: collapse;
	}
</style>
<!-- <table cellpadding="" cellspacing=""></table> -->
<div class="continer-fluid">
	<div class="watermark">
		<span>
			<?= Yii::t('rms', Yii::$app->params['project']) . '<br>' . Yii::t('rms', Yii::$app->params['client']) . '<br>' . Yii::t('rms', Yii::$app->params['college'])  ?>
		</span>
	</div>
	<div class="row" style="justify-content: center;">
		<div class="col-md-2">
			&nbsp;&nbsp;&nbsp;&nbsp;
		</div>
		<div class="col-md-8 diagonal-text" style=" background-color: white; border: 1px dashed black; padding: 10px;" id="printableDiv">

			<?php

			$semregObject = \frontend\models\SemesterRegistration2::findOne($content);

			$report = "";

			if ($semregObject) {
				if ($semregObject->semreg_entry_type == 2) {
					echo "<span>Transfer</span>";
				}
				// $gset = \frontend\models\SemesterRegistration2Detail::findOne()
				$report .= getHeader($semregObject);

				$report .= getEnrolledCourseAll($semregObject);

				$report .= getFormFooter();
			} else {
				$content = "<p class='bg-danger'>Invalid Student semester registration Entry.</p>";
			}

			echo "{$report}";

			function getFormFooter()
			{

				return "

		        	";
			}
			function getHeader($semregObject)
			{

				// get to which group did the stident was asigned in that semster and clslvl.
				$std = \backend\models\Student::findOne($semregObject->semreg_std_relId);
				$user = \common\models\User1::findOne($std->std_user_relId);

				$semregDetail = \frontend\models\SemesterRegistration2Detail::find()
					->where(['srd2_semreg_relId' => $semregObject->id])
					->one();

				if ($semregDetail) {
				} else {
					return "No Course enrolled.";
				}
				$gset = \frontend\models\SemesterRegistration2Detail::find()
					->where(['srd2_semreg_relId' => $semregObject->id])
					->one()->srd2_group_relId;

				$gset = \backend\models\WGroupSetup::findOne($gset);

				$dept = \backend\models\Department::findOne($std->std_dept_relId)->dept_name;
				$adm = \backend\models\Admission::findOne($std->std_admission_relId)->admission_name;
				$prog = \backend\models\Program::findOne($std->std_prog_relId)->program_name;
				$spec = is_null($gset->gs_spec_relId) ? '-' : \backend\models\Specialization::findOne($gset->gs_spec_relId)->spec_name;
				$clslvl = \backend\models\ClassLevel::findOne($gset->gs_clslvl_relId)->clslvl_num;

				$year = \backend\models\SchoolYear::findOne($gset->gs_acadYear_relId)->sch_year_desc;
				$sem = \backend\models\Semester::findOne($gset->gs_sem_relId)->semester_name;
				return "
		            <div class='contianer-fluid' style='justify-content: center; text-align:center;'>
		            	<h3>" . strtoupper(\Yii::$app->params['client']) . ", " . strtoupper(Yii::$app->params['college']) . "</h3>
		            	<h4><u>Semester Registration Report</u></h4>

			            <b>Student Full Name</b> : <u>{$user->first_name} {$user->middle_name} {$user->last_name}</u><br>

			            <b>Admission</b> : <u>{$adm} {$prog}</u> &nbsp;&nbsp;&nbsp;&nbsp;
			            <b>Department</b> : <u>{$dept}</u><br>
			            <b>Specialization</b> : <u>{$spec}</u>&nbsp;&nbsp;&nbsp;&nbsp;
			            <b>Class Level</b> : <u>{$clslvl}</u><br>

			            Semester <i><u>{$sem}</u></i> of <i><u>{$year}</u></i>
		            </div>
		        ";
			}

			function getEnrolledCourseAll($semregObject)
			{
				if ($semregObject) {
					# code...
				} else {
					return "Not Enrollement history found.";
				}
				$hdName = '  ';
				$semregDetail = \backend\models\SemesterRegistration2Detail::find()
					->where(['srd2_semreg_relId' => $semregObject->id])
					->all();
				$report  = "";

				if ($semregDetail && count($semregDetail)) {

					$gset2 = \backend\models\WGroupSetup::findOne($semregDetail[0]->srd2_group_relId);
					$advisorObj = new \backend\models\SemesterAdvisor();
					$advisor = $advisorObj->getSemesterAdvisor($gset2);
					if ($advisor && count($advisor) > 0) {
						$hodInst = backend\models\Instructor::findOne($advisor[0]->sa_inst_relId)->inst_user_relId;

						$user = \common\models\User1::findOne($hodInst);

						$hdName = $user->first_name . ' ' . $user->middle_name;
					}

					$totalCrdt = 0;
					$totalLabHr = 0;
					$totalLectHr = 0;
					$totalTutHr = 0;
					$report .= "<table class='table table-condensed' cellpadding='3' cellspacing='0' style='font-size:9pt;'>
		                    <thead style='background-color: #8D0;'>
		                        <th style='border:1px solid black;' colspan = 2>Course Code</th>
		                        <th style='border:1px solid black;'>Course Title</th>
		                        <th style='border:1px solid black;'>Crdt. Hr.</th>
		                        <th style='border:1px solid black;'>Lect. Hr.</th>
		                        <th style='border:1px solid black;'>Lab. Hr.</th>
		                        <th style='border:1px solid black;'>Tut. Hr.</th>
		                        <th style='border:1px solid black;'>Catagory</th>
		                        <th style='border:1px solid black;'>Remark</th>
		                    </thead><tbody>
		                    	<tr>
		                    		<td rowspan = " . (count($semregDetail) + 1) . " style='border:1px solid black;'>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;</td>
		                    	</tr>
		                    ";

					foreach ($semregDetail as $key) {
						$course = \backend\models\Course::findOne($key->srd2_course_relId);
						$gset = \backend\models\WGroupSetup::findOne($key->srd2_group_relId);
						$coff2Det = \backend\models\CourseOffering2Detail::findOne([
							'coff2det_coff2_relId' => $key->srd2_coff_relId,
							'coff2det_course_relId' => $key->srd2_course_relId,
						]);

						if ($coff2Det) {
							$catag = $coff2Det->coff2detCocatRel;
							if ($catag) {
								# code...
							} else {
								$coff2Det->delete();
								continue;
							}
						} else {
							// this student enrolled the course that is NOT offered, or removed after it have been enrolled
							$key->delete();
							continue;
						}

						$cadd = is_null($key->srd2_coadd_flag) ? '' : 'Added';
						$rptd = $key->srd2_corptd_flag == 0 ? '' : '*';

						$catag = $catag->cocat_courseMajor == 1 ? 'Major' : 'Common';
						$totalCrdt += $course->course_crdthr;
						$totalLabHr += $course->course_lab_hour;
						$totalLectHr += $course->course_lecture_hour;
						$totalTutHr += $course->course_tutorial_hour;
						$report .= "<tr>
		                                <td style='border:1px solid black;white-space: nowrap;'>{$course->course_code}</td>
		                                <td style='border:1px solid black;'>{$course->course_title}{$rptd}</td>
		                                <td style='border:1px solid black; text-align:center;'>{$course->course_crdthr}</td>
		                                <td style='border:1px solid black; text-align:center;'>{$course->course_lecture_hour}</td>
		                                <td style='border:1px solid black; text-align:center;'>{$course->course_lab_hour}</td>
		                                <td style='border:1px solid black; text-align:center;'>{$course->course_tutorial_hour}</td>
		                                <td style='border:1px solid black; text-align:center;'>{$catag}</td>
		                                <td style='border:1px solid black; text-align:center;'>{$cadd}</td>
		                            </tr>";
					}

					$sLoad = \backend\models\SemesterRegistrationControld::getLoadInterval($gset, $totalCrdt);
					$report .= "
		                    <tr>
		                        <td style='font-style:italic; border:1px solid black; text-align:center;' colspan=3>
		                            <strong>Total Cedit Hour.</strong>
		                        </td>
		                        <td style='font-style:italic; border:1px solid black; text-align:center;'>
		                            <strong><u>{$totalCrdt}</u></strong>
		                        </td>
		                        <td style='font-style:italic; border:1px solid black; text-align:center;'>
		                            <strong><u>{$totalLectHr}</u></strong>
		                        </td>
		                        <td style='font-style:italic; border:1px solid black; text-align:center;'>
		                            <strong><u>{$totalLabHr}</u></strong>
		                        </td>
		                        <td style='font-style:italic; border:1px solid black; text-align:center;'>
		                            <strong><u>{$totalTutHr}</u></strong>
		                        </td>
		                        <td style='font-style:italic; text-align:left; font-size: 7pt; font-weigh:normal' colspan=2>
		                            <strong>Key: ADDED (if any in remark entry) indicates the course is Added.</strong>
		                        </td>
		                    </tr>
		                    <tr> <td colspan = 8><br><br></td> </tr>
		                    <tr>
		                    	<td></td>
		                    	<td style='font-style:italic; text-align:left; font-size: 8pt;' colspan=2>
		                            <strong>Advisor: <br><br><br> <u>{$hdName}</u></strong>
		                        </td>
		                        <td style='font-style:italic; text-align:left; font-size: 8pt;' colspan=2>
		                            <strong>
			                            Sign: <br><br><br>
		                            		______________
		                            </strong>
		                        </td>
		                        <td style='font-style:italic; text-align:left; font-size: 8pt;' colspan=2>
		                            <strong>
		                            	Registration Date:<br><br><br>

		                            	<u>" . date_format(date_create($key->srd2_created_at), 'd M, Y h:i') . "</u>
		                            </strong>
		                        </td>
		                        <td style='font-style:italic; text-align:left; font-size: 8pt;' colspan=2>
		                            <strong>
		                            	Semester Load: <u>{$sLoad}</u>
		                            	<br><br><br>

		                            	Generated On: <u>" . date('d M, Y h:i') . "</u>

		                            </strong>
		                        </td>
		                    </tr>
		                    <tr style='display:none;'>
		                    	<td colspan = 9><button class='btn btn-xs btn-primary non-print pull-right' onclick='printIcon()'>Print</button></td>
		                    </tr>
		                </tbody>
		            </table>";
					return $report;
				} else {
					return "Enrollement history NOT found.";
				}
			}
			?>
		</div>
	</div>
</div>

<script type="text/javascript">
	window.printIcon = function() {
		$('.non-print').hide();
		var restorePage = $('body').html();
		var printContent = $('#printableDiv').html();
		$('body').html(printContent);
		window.print();
		$('body').html(restorePage);
		$('.non-print').show();
	}
</script>