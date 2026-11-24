<?php


/*
	This is report generator for course offering detail for the semester.
	The code is self-descriptive and clearly designed.

	Saturday 02-06-2019
			wondetom@gmail.com
*/

namespace frontend\controllers;

use backend\models\StudentSemesterStatusHistory;
use backend\models\CourseOffering2Detail;

class CourseOfferingReportController extends \yii\web\Controller
{
	public function actionIndex()
	{
		$model = new StudentSemesterStatusHistory();
		return $this->render('index', ['model' => $model]);
	}

	public function actionGetOfferedCoursesList()
	{
		$acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die('Select Academic Year');
		$semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('Select Semester');
		$admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Select Admission');
		$progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Select Program');
		// $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])?intval($_POST['clslvlId']):die('Select Class Year.');
		$deptId = isset($_POST['deptId']) && !empty($_POST['deptId']) ? intval($_POST['deptId']) : die('Select Department.');

		$coffEntry = \backend\models\CourseOffering2::find();
		$coffEntry = $coffEntry->innerJoin('w_group_setup', 'w_group_setup.id = course_offering2.coff2_group_relId');
		$coffEntry = $coffEntry->filterWhere([
			'w_group_setup.gs_acadYear_relId' => $acadYearId,
			'w_group_setup.gs_sem_relId' => $semId,
			'w_group_setup.gs_prog_relId' => $progId,
			'w_group_setup.gs_adm_relId' => $admId,
			'w_group_setup.gs_dept_relId' => $deptId,
		]);

		$coffEntry = $coffEntry->andFilterWhere([
			'coff2_acadYear_relId' => $acadYearId,
			'coff2_sem_relId' => $semId,
		]);

		if (isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])) {
			$coffEntry = $coffEntry->andFilterWhere(['w_group_setup.gs_clslvl_relId' => intval($_POST['clslvlId'])]);
		}

		if (isset($_POST['spec']) && !empty($_POST['spec']) && $_POST['spec'] != 0) {
			$coffEntry = $coffEntry->andFilterWhere(['w_group_setup.gs_spec_relId' => intval($_POST['spec'])]);
		}

		// $coffEntry = $coffEntry->indexBy('w_group_setup.gs_clslvl_relId');
		$coffEntry = $coffEntry->all();

		// var_dump($coffEntry);die();

		$coffEntryArray = [];
		if ($coffEntry && count($coffEntry) > 0) {
			foreach ($coffEntry as $key) {
				$coffEntryArray[] = $key->id;
			}
		}

		$table = "";
		$adm = \backend\models\Admission::findOne($admId);
		$prog = \backend\models\Program::findOne($progId);
		$dept = \backend\models\Department::findOne($deptId);
		$acad = \backend\models\SchoolYear::findOne($acadYearId);
		$sem = \backend\models\Semester::findOne($semId);
		$spec = isset($_POST['spec']) && !empty($_POST['spec']) && $_POST['spec'] != 0 ? \backend\models\Specialization::findOne($_POST['spec'])->spec_name : '-';

		if (!empty($coffEntryArray)) {
			$offeredCourses = \backend\models\CourseOffering2Detail::find()
				->where(['coff2det_coff2_relId' => $coffEntryArray]);

			if (isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])) {
				$offeredCourses = $offeredCourses->groupBy('coff2det_course_relId');
			}
			$offeredCourses = $offeredCourses->all();


			if ($offeredCourses && count($offeredCourses) > 0) {

				if (!(isset($_POST['clslvlId']) && !empty($_POST['clslvlId']))) {
					// repeate for each class level
					$table .= "<table class='table table-bordered table-striped table-condensed table-hover'>";
					$table .= "<caption style='text-align:center;'>
		        				<h4><u>Offered Courses Lists</u></h4>
		        				<p>
		        				<b>Admission:</b><u>{$adm->admission_name}</u>&nbsp;&nbsp;&nbsp;&nbsp;
		        				<b>Program:</b><u>{$prog->program_name}</u><br>
		        				<b>Department:</b><u>{$dept->dept_name}</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		        				<b>Specialization:</b><u>{$spec}<br>
		        				Semester {$sem->semester_name} of {$acad->sch_year_desc} Academic Year.
		        				</p>
		        			</caption>";
					$table .= "<thead style='background-color:#8D0;'>
		        			<th>#</th>
		        			<th>Course Code</th>
		        			<th>Course Title</th>
		        			</thead><tbody>";
					$count = 0;
					// get each active baches for the department, and the groups assigned for these batches.
					$gset = \backend\models\WGroupSetup::find()
						->where([
							'gs_adm_relId' => $admId,
							'gs_prog_relId' => $progId,
							'gs_dept_relId' => $deptId,
						])
						->groupBy('gs_clslvl_relId')
						->orderBy('gs_clslvl_relId')
						->all();

					// echo "<p class='bg-danger'>Groups under each batches for {$deptId}</p>";

					foreach ($gset as $key) {
						// get class level first, and the I will tell what to do.
						$clslvl = \backend\models\ClassLevel::findOne($key->gs_clslvl_relId);

						$c = $clslvl->clslvl_num == 1 ? '1<sup>st</sup>' : ($clslvl->clslvl_num == 2 ? '2<sup>nd</sup>' : ($clslvl->clslvl_num == 3 ? '3<sup>rd</sup>' : $clslvl->clslvl_num . '<sup>th</sup>'));
						// and then get the courses offered for this group
						$table .= "<tr><td colspan=3 style='text-align:center; background-color:#8D0;'>$c YEAR</td></tr>";
						$coffDet = \backend\models\CourseOffering2Detail::find()
							->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
							->where(['course_offering2.coff2_group_relId' => $key->id])
							->all();
						if ($coffDet && count($coffDet) > 0) {
							$count1 = 0;
							foreach ($coffDet as $key) {
								$course = \backend\models\Course::findOne($key->coff2det_course_relId);
								$table .= "
			   						<tr>
			        					<td>" . ++$count1 . "</td>
			        					<td>{$course->course_code}</td>
			        					<td style='text-align:left;'>{$course->course_title}</td>
			        				</tr>";
							}
						} else {
							$table .= "<tr><td colspan=3 style='text-align:center;'><p class='bg-danger'><i>Course Not offered on selected semester.</i></p></td></tr>";
						}
					}
					$table .= "</tbody></table>";
					echo "{$table}";
					die();
				} else {
					$clslvl = \backend\models\ClassLevel::findOne($_POST['clslvlId']);
					$table .= "<table class='table table-bordered table-striped table-condensed table-hover'>";
					$table .= "<caption style='text-align:center;'>
		        				<h4><u>Offered Courses Lists</u></h4>
		        				<p>
		        				<b>Admission:</b><u>{$adm->admission_name}</u>&nbsp;&nbsp;&nbsp;&nbsp;
		        				<b>Program:</b><u>{$prog->program_name}</u><br>
		        				<b>Department:</b><u>{$dept->dept_name}</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
		        				<b>Class Level:</b><u>{$clslvl->clslvl_num}</u><br>
		        				<b>Specialization:</b><u>{$spec}
		        				</p>
		        			</caption>";
					$table .= "<thead style='background-color:#8D0;'>
		        			<th>#</th>
		        			<th>Course Code</th>
		        			<th>Course Title</th>
		        			</thead><tbody>";
					$count = 0;
					foreach ($offeredCourses as $key) {
						// get number of courses enrolled by each student.
						// $std = \backend\models\Student::findOne($key->semreg_std_relId);
						// $user = \backend\models\User::findOne($std->std_user_relId);

						$course = \backend\models\Course::findOne($key->coff2det_course_relId);

						// $courseNum = $this->getEnrolledCourse($key->id,$key->semreg_std_relId);
						$table .= "<tr>
		        					<td>" . ++$count . "</td>
		        					<td>{$course->course_code}</td>
		        					<td>{$course->course_title}</td>
		        				</tr>";
					}
					$table .= "</tbody></table>";
					echo "{$table}";
					die();
				}
			} else {
				echo "<p class='bg-danger'>No record found.</p>";
				die();
			}
		} else {
			echo "<p class='bg-danger'>No Course offered.</p>";
			die();
		}
	}

	public function actionGetOfferedCoursesList2()
	{
		$acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die('Select Academic Year');
		$semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('Select Semester');
		$admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Select Admission');
		$progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Select Program');
		$deptId = isset($_POST['deptId']) && !empty($_POST['deptId']) ? intval($_POST['deptId']) : die('Select Department.');
		$clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : NULL;

		// var_dump();

		$coffTempModel = new \backend\models\CourseOfferingTemp();

		$offeredCourses = NULL;

		if (is_null($clslvlId)) {
			$offeredCourses = $coffTempModel->find()
				->where([
					'cot_adm_relId' => $admId,
					'cot_prog_relId' => $progId,
					'cot_acadYear_relId' => $acadYearId,
					'cot_sem_relId' => $semId,
					'cot_dept_relId' => $deptId,
				]);
		} else {
			$offeredCourses = $coffTempModel->find()
				->where([
					'cot_adm_relId' => $admId,
					'cot_prog_relId' => $progId,
					'cot_acadYear_relId' => $acadYearId,
					'cot_sem_relId' => $semId,
					'cot_dept_relId' => $deptId,
					'cot_clslvel_relId' => $clslvlId,
				]);
		}

		if (isset($_POST['spec']) && !empty($_POST['spec'])) {
			$offeredCourses = $offeredCourses->andWhere([
				'cot_spec_relId' => intval($_POST['spec'])
			]);
		}
		$offeredCourses = $offeredCourses->groupBy('cot_clslvel_relId')
			->all();

		// var_dump($offeredCourses);die();

		if ($offeredCourses && count($offeredCourses) > 0) {
			$table = "";
			$adm = \backend\models\Admission::findOne($admId);
			$prog = \backend\models\Program::findOne($progId);
			$dept = \backend\models\Department::findOne($deptId);
			$acad = \backend\models\SchoolYear::findOne($acadYearId);
			$sem = \backend\models\Semester::findOne($semId);
			$spec = isset($_POST['spec']) && !empty($_POST['spec']) && $_POST['spec'] != 0 ? \backend\models\Specialization::findOne($_POST['spec'])->spec_name : '-';
			$cls = is_null($clslvlId) ? ' - ' : \backend\models\ClassLevel::findOne($clslvlId)->clslvl_num;

			$table .= "<table class='table table-bordered table-striped table-condensed table-hover'>";
			$table .= "<caption style='text-align:center;'>
        				<h4><u>Offered Courses Lists</u></h4>
        				<p>
        				<b>Admission:</b><u>{$adm->admission_name}</u>&nbsp;&nbsp;&nbsp;&nbsp;
        				<b>Program:</b><u>{$prog->program_name}</u><br>
        				<b>Department:</b><u>{$dept->dept_name}</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
        				<b>Class Level:</b><u>{$cls}</u><br>
        				<b>Specialization:</b><u>{$spec}
        				</p>
        			</caption>";
			$table .= "<thead style='background-color:#8D0;'>
        			<th>#</th>
        			<th>Course Code</th>
        			<th>Course Title</th>
        			<th>Course Dept.</th>
        			<th>Crdt Hr.</th>
        			</thead><tbody>";
			$count = 0;
			foreach ($offeredCourses as $offeredCourse) {
				$cslvlNum = $this->getClslvlNum($offeredCourse->cot_clslvel_relId);
				$spec = $offeredCourse->cot_spec_relId ? "(Stream: {$offeredCourse->cotSpecRel->spec_name})" : "(Stream: N/A)";
				$table .= "<tr>
	        				<td style='background-color:#9DC;text-align:center;' colspan = 5>{$cslvlNum}<i>{$spec}</i></td>
        				</tr>";
				if ($offeredCourse->cot_cocat_IDS) {
					$table .= $this->disributeCourses($offeredCourse, $offeredCourse->cot_clslvel_relId);
				} else {
					$table .= "<tr>
			        				<td style='background-color:#30C' colspan = 5>Course Not Offered</td>
		        				</tr>";
				}
			}

			$table .= "</tbody></table>";

			echo "{$table}";
			die();
		} else {
			echo "<p class='bg-danger'>No Record Found</p>";
			die();
		}
	}

	private function disributeCourses($cotModel, $clslvlKey)
	{
		// var_dump(json_decode($courses->cot_cocat_IDS,true));die();
		$coccatIds = array_unique(json_decode($cotModel->cot_cocat_IDS, true)); //encoded cocat Ids.

		if (!($coccatIds && count($coccatIds) > 0)) {
			return "<tr><td style='background-color:#F5C' colspan = 4 style = 'text-align:center;'>Course Matrix Error</td></tr>";
		} else {
			$rr = "";
			$count = 0;
			$total = 0;
			foreach ($coccatIds as $COCATkey) {
				$coatObj = \backend\models\CourseCatalog::findOne($COCATkey);
				if (!$coatObj) { //the courses matrix is removed:
					/*remove the course from offering history*/
					$c_IDS = array_diff(json_decode($cotModel->cot_cocat_IDS, true), [$COCATkey]);

					$cotModel->cot_cocat_IDS = json_encode($c_IDS);
					$cotModel->update(); //deletes from teh temporary:

					// how to remove from offering detail?
					// coff2det_coca
					// coff2det_cocat_relId
					$models = CourseOffering2Detail::find()->where(['coff2det_cocat_relId' => $COCATkey])->all();

					// course offered by this catalogue, so remove all::
					foreach ($models as $coffDetModel) {
						$coffDetModel->delete();
					}
					continue;
				}
				$rr .= "
    				<tr>
    					<td>" . ++$count . "</td>
    					<td>{$coatObj->cocatCourseRel->course_code}</td>
    					<td>{$coatObj->cocatCourseRel->course_title}</td>
    					<td>{$coatObj->cocatCourseRel->courseDeptRel->dept_code}</td>
    					<td>{$coatObj->cocatCourseRel->getCourseCredits()}</td>
    				</tr>
    				";

				$total += $coatObj->cocatCourseRel->course_crdthr;
			}
			$rr .= "
            <tr style='font-weight:bold; text-decoration:underline;'>
              <td style='text-align:center;' colspan=4>Total</td>
              <td>{$total}</td>
            </tr>
            ";
		}
		return $rr;
	}

	private function getClslvlNum($clslvlKey)
	{
		$clslvlObj = \backend\models\ClassLevel::findOne($clslvlKey);

		return $clslvlObj->getClslvlDisp();
	}
}
