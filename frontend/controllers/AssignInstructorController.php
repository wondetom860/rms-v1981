<?php

namespace frontend\controllers;

use yii;
use yii\helpers\ArrayHelper;
use \common\components\CommonControllerFunctions as CCF;
use \common\models\User;

class AssignInstructorController extends \yii\web\Controller
{

	public $acadYearId;
	public $semId;
	protected function __constructor()
	{

		$cs = CCF::currentTime();
		$this->acadYearId = $cs['Y'];
		$this->semId = $cs['S'];
	}
	public function actionIndex()
	{
		return $this->render('index');
	}

	public function actionAssignInstructor()
	{
		// ,,taId

		if (Yii::$app->user->can('DepartmentHead')) {
			$coffdetId = isset($_POST['coffdetId']) && !empty($_POST['coffdetId']) ? intval($_POST['coffdetId']) : die("Course selection error!, Please select One.");
			$instId = isset($_POST['instId']) && !empty($_POST['instId']) ? intval($_POST['instId']) : die("Instructor NOT Known!, Please select One.");
			$taId = isset($_POST['taId']) && !empty($_POST['taId']) ? intval($_POST['taId']) : NULL;

			$coffdet = \backend\models\CourseOffering2Detail::findOne(['id' => $coffdetId, 'coff2det_inst_relId' => NULL]);

			if ($coffdet) {
				// $course = \backend\models\Course::findOne(['id'=>$coffdet->coff2det_course_relId]);
				// if ($course->course_lab_hour > 0 && !(isset($_POST['taId']) && !empty($_POST['taId']))) {
				// 	die("The course should have Lab assistant assigned, Canni");
				// }
				$result = Yii::$app->db->createCommand()
					->update(
						'course_offering2_detail',
						[
							'coff2det_inst_relId' => $instId,
							'coff2det_TA_relId' => $taId,
						],
						[
							'id' => $coffdetId,
						]
					)
					->execute();

				if ($result) {
					$coffdet->updateFaclutyAssignmentData();
					echo 1;
				} else {
					die('Failed to assign Instructor, Please re-try later.');
				}
			} else {
				die("<p class='bg-danger'>Uknown request, such course offering not found.</p>");
			}
		} else {
			echo "<p class='bg-danger'>No privilage to access this.</p>";
			die();
		}
	}

	private function drawTable2($coffObject, $form, $instDept)
	{

		$offerDepts = $coffObject;
		if ($offerDepts && count($offerDepts) > 0) {

			$table = '';
			$table .= "<table class='table table-sm table-bordered table-hover' style='font-size:9pt;' >";
			$table .= "<caption>Active Courses in this semester.</caption>";
			$table .= " <thead style='background-color:#ace;'>
					<th>#</th>
					<th>Dept.</th>
					<th>Program</th>
					<th>Group</th> 
					<th>Course Title</th> 
					<th>Instructor</th>
					<th>Technical Assistant</th>";
			if ($form == 0) {
				$table .= "<th>Option</th>";
			}
			$table .= "<tbody>";
			$count = 0;

			foreach ($offerDepts as $key) {
				$gset = \backend\models\CourseOffering2::findOne(['id' => $key->coff2det_coff2_relId])->coff2_group_relId;
				$deptSelectTA = "";
				$deptSelectINST = "";

				$gset = \backend\models\WGroupSetup::findOne(['id' => $gset]);

				if ($gset) {
					$adm = \backend\models\Admission::findOne(['id' => $gset->gs_adm_relId])->admission_name;
					$prog = \backend\models\Program::findOne(['id' => $gset->gs_prog_relId])->program_name;
					$dept = \backend\models\Department::findOne(['id' => $gset->gs_dept_relId]);
					// $clslvl = \backend\models\ClassLevel::findOne(['id'=>$gset->gs_clslvl_relId])->clslvl_num;

					// $spec = is_null($gset->gs_spec_relId)?'-':\backend\models\Specialization::findOne(['id'=>$gset->gs_spec_relId])->spec_name;
					// $gname = \backend\models\WGroup::findOne(['id'=>$gset->gs_group_relId])->group_name;

					$course = \backend\models\Course::findOne(['id' => $key->coff2det_course_relId]);

					$name = NULL;

					if (is_null($key->coff2det_inst_relId)) {

						$deptSelectINST = "<select class='form-control' title='Filter Instructors by their department' onchange='filterByDeptForINST($(this).val(),{$key->id})'>
						<option value=''> - </option>";
						$depts = \backend\models\Department::find()->all();

						if ($depts && count($depts) > 0) {
							foreach ($depts as $DPTkey) {
								if ($DPTkey->id == $instDept) {
									$deptSelectINST .= "<option value={$DPTkey->id} selected='true'>{$DPTkey->dept_name}</option>";
									continue;
								}
								$deptSelectINST .= "<option value={$DPTkey->id}>{$DPTkey->dept_name}</option>";
							}
						}
						$deptSelectINST .= "</select>";
						$name = $this->getInst("INST", $course, $instDept, $key);
					} else {
						$inst = \backend\models\Instructor::findOne(['id' => $key->coff2det_inst_relId]);

						// $user= \backend\models\User::findOne(['id'=>$inst]);

						$name = $inst->getFullName();
					}
					$catalog = $key->coff2detCocatRel;
					$proj = $catalog && $catalog->cocat_courseProject ? " <span class='badge badge-info' title='This is a project course.'>P</span>" : "";

					$nameTA = NULL;

					if (is_null($key->coff2det_TA_relId)) {
						// check if the TA is required.

						$nameTA = $this->getInst("TA", $course, $instDept, $key);
						if ($nameTA != 'N/A') {
							$deptSelectTA = "<select class='form-control' title='Filter TAs by their department' onchange='filterByDeptForTA($(this).val(),$key->id)'>
							<option value=''> - </option>";
							$depts = \backend\models\Department::find()->all();

							if ($depts && count($depts) > 0) {
								foreach ($depts as $DPTkey) {
									if ($DPTkey->id == $instDept) {
										$deptSelectTA .= "<option value={$DPTkey->id} selected='true'>{$DPTkey->dept_name}</option>";
										continue;
									}
									$deptSelectTA .= "<option value={$DPTkey->id}>{$DPTkey->dept_name}</option>";
								}
							}
							$deptSelectTA .= "</select>";
						}
					} else {
						$instTA = \backend\models\Instructor::findOne(['id' => $key->coff2det_TA_relId]);

						// $userTA= \backend\models\User::findOne(['id'=>$instTA]);

						$nameTA = $instTA->getFullName();
					}


					$table .= " <tr>
						<td>" . ++$count . "</td>
						<td title='{$dept->dept_name}'>{$dept->dept_code}</td>
						<td>{$prog}(<b>{$adm[0]}</b>)</td>
						<td style='text-align:center;'>{$gset->getGsDisp()}</td>
						<td><b>{$course->course_title}{$proj}</b><br><small style='padding-left: 5px;'>({$course->getCourseCredits()})</small></td>
						<td>
							{$deptSelectINST}					
							{$name}
						</td>
						<td>
							{$deptSelectTA}
							{$nameTA}
						</td>";
					if ($form == 0) {
						$button = "<button class='btn btn-success btn-xs' onclick='assignInstructorFor({$key->id})'>Save</button>";
						$table .= "<td>{$button}</td>";
					}
					$table .= "</tr>";
				} else {
					continue;
				}
			}
			$table .= "</tbody></table>";

			return $table;
		} else {
			return "<p class='bg-danger'>No result.</p>";
		}
	}

	private function getInst($instType, $course, $deptId, $key)
	{
		$nameTA = "N/A";
		$nameINST = "TBA";
		if ($instType == 'TA') {
			if ($course->course_lab_hour > 0) {
				$instSelectTA = "<select class='form-control' title='Select TA.' id='{$key->id}_TA'>";
				$instructorsTA = \backend\models\Instructor::find()
					->where(['inst_dep_relId' => $deptId])
					->innerJoin('user', 'user.id = instructor.inst_user_relId')
					->andWhere(['user.status' => User::STATUS_ACTIVE])
					->andWhere(['user.activated' => 1])
					->all();

				$instSelectTA .= "<option value=''> - </option>";
				foreach ($instructorsTA as $key2) {
					$instSelectTA .= "<option  title='{$key2->instAcrRel->acr_name}' value='{$key2->id}'>{$key2->getFullName()}</option>";
				}
				$instSelectTA .= "</select>";
				$nameTA = $instSelectTA;
			} else {
				$nameTA = "N/A";
			}

			return $nameTA;
		} else {
			$instSelect = "<select class='form-control' title='Select Instructor' id='{$key->id}_INST'>";

			$instructors = \backend\models\Instructor::find()
				->where(['inst_dep_relId' => $deptId])
				->innerJoin('user', 'user.id = instructor.inst_user_relId')
				->innerJoin('academic_rank', 'academic_rank.id = instructor.inst_acq_relId')
				->andWhere(['user.status' => User::STATUS_ACTIVE])
				->andWhere(['user.activated' => 1])
				->all();
			$instSelect .= "<option value=''> - </option>";
			foreach ($instructors as $key2) {
				$instSelect .= "<option title='{$key2->instAcrRel->acr_name}' value='{$key2->id}'>{$key2->getFullName()}</option>";
			}

			$instSelect .= "</select>";

			return $instSelect;
		}
	}

	public function actionGetCourseOfferingDepts()
	{
		if (Yii::$app->user->can('DepartmentHead')) {
			$instDept = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id])->inst_dep_relId;
			// Get departmetns which offer his/her dept courses in this semester.
			$this->__constructor();

			$offerDepts = \backend\models\CourseOffering2Detail::find()
				->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
				->innerJoin('course_catalog', 'course_catalog.id = coff2det_cocat_relId')
				->innerJoin('course', 'course.id = course_offering2_detail.coff2det_course_relId')
				->where([
					'coff2_acadYear_relId' => $this->acadYearId,
					'coff2_sem_relId' => $this->semId
				])
				->andWhere(['course.course_dept_relId' => $instDept])
				->andWhere(['<>', 'cocat_courseProject', 1])
				->andWhere(['coff2det_inst_relId' => NULL])
				->all();
			return $this->drawTable2($offerDepts, 0, $instDept);
		} else {
			die("<p class='bg-danger'>You have no privilage for this action.</p>");
		}
	}

	public function actionGetAssignmentReport()
	{
		if (Yii::$app->user->can('DepartmentHead')) {
			$instDept = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id])->inst_dep_relId;
			// Get departmetns which offer his/her dept courses in this semester.
			$this->__constructor();

			$offerDepts = \backend\models\CourseOffering2Detail::find()
				->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
				->where(['coff2_acadYear_relId' => $this->acadYearId, 'coff2_sem_relId' => $this->semId])
				->innerJoin('course', 'course.id = course_offering2_detail.coff2det_course_relId')
				->andWhere(['course.course_dept_relId' => $instDept])
				->andWhere(['IS NOT', 'coff2det_inst_relId', NULL])
				->all();
			return $this->drawTable2($offerDepts, 1, $instDept);
		} else {
			die("<p class='bg-danger'>You have no privilage for this action.</p>");
		}
	}
}
