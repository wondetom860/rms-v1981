<?php

namespace frontend\controllers;

use yii;
use yii\helpers\ArrayHelper;
use backend\models\AddHistory;
use frontend\models\Switch2;
use backend\models\PreRequisitController;
use backend\models\CourseOffering2Detail;
use backend\models\CourseOffering;
use backend\models\CourseCatalog;
use backend\models\WGroupSetup;
use backend\models\WGroup;
use backend\models\Student;
use backend\models\Course;
use frontend\models\SemesterRegistration2;
use frontend\models\SemesterRegistration2Detail;
use \common\components\CommonControllerFunctions as CCF;

class CourseAddController extends \yii\web\Controller
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
		// return $this->render('index');
		$model = new AddHistory();
		return $this->render('index', [
			'model' => $model,
		]);
	}

	// stores the add request detail into table
	public function actionCreate2()
	{
		// offeringDetId:offeringDetId,stdId:stdId

		$stdId = isset($_POST['stdId']) && !empty($_POST['stdId']) ? $_POST['stdId'] : die('Uknown Student profile Entry');
		$offeringDetId = isset($_POST['offeringDetId']) && !empty($_POST['offeringDetId']) ? intval($_POST['offeringDetId']) : die('Uknown Course Offering entry');

		$model = new AddHistory();
		$this->__constructor();
		$std = Student::findOne($stdId);
		$actionName = 'Course add';
		$gset = $std->stdGroupReal;
		$action = \backend\models\AcademicCalanderAction::findOne(['aca_name' => $actionName]);
		if ($action) {
			$schedule = CCF::getScheduleForAction($gset, $action->id);
			if (!($schedule)) {
				die('Schedule is not open for course adding.');
			}
		} else {
			die("Unregistered action {$actionName}");
		}
		// get student registration relId, srd2_id,
		$coffDet = CourseOffering2Detail::findOne($offeringDetId);

		$gsetObject = $coffDet->coff2detCoff2Rel->coff2GroupRel;
		$courseObject = $coffDet->coff2detCourseRel;

		$coff32 = $this->checkOffered($gsetObject, $courseObject);

		if (!($coff32 && count($coff32) > 0)) {
			die('Invalid selection please correct.');
		}
		// See if such record already exists; enrolled first.
		$requests = AddHistory::find()
			->where([
				'ah_std_relId' => $std->id,
				'ah_course_relId' => $courseObject->id,
				'ah_approved' => 0
			])
			->all();

		$model->ah_course_relId = $courseObject->id;
		$model->ah_addWithGroup_relId = $gsetObject->id;
		$model->ah_std_relId = $std->id;
		$model->ah_sem_relId = $this->semId;
		$model->ah_acadYear_relId = $this->acadYearId;
		$model->ah_srd2_relId = NULL;
		$model->ah_approved_by = NULL;
		$model->ah_approved_at = NULL;
		$model->ah_coff_relId =  $coffDet->id;

		$model->ah_created_by = $model->ah_updated_by = Yii::$app->user->identity->id;
		$model->ah_created_at = $model->ah_updated_at = date('Y-m-d h:i:s');

		// var_dump($model);die();
		if ($model->save()) {
			return 1;
		} else {
			return json_encode($model->getErrors());
		}
	}

	public function actionCreate()
	{
		$addCourse = isset($_POST['addCourse1']) && !empty($_POST['addCourse1']) ? $_POST['addCourse1'] : die('Uknown course selected.');
		$addWithGroup = isset($_POST['addWithGroup']) && !empty($_POST['addWithGroup']) ? $_POST['addWithGroup'] : die('Uknown group');
		$addWithclslvl = isset($_POST['addWithClslvl']) && !empty($_POST['addWithClslvl']) ? $_POST['addWithClslvl'] : die('Uknown group');
		$addWithCoff = isset($_POST['addWithCoff']) && !empty($_POST['addWithCoff']) ? intval($_POST['addWithCoff']) : die('Uknown Course Offering entry');

		// print_r([$addCourse,$addWithclslvl, $addWithGroup]);die();

		$model = new AddHistory();
		$this->__constructor();
		// get student registration relId, srd2_id,
		$std = Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
		$gsetObject = WGroupSetup::findOne($addWithGroup);
		$courseObject = Course::findOne($addCourse);

		$coff32 = $this->checkOffered($gsetObject, $courseObject);

		if (!($coff32 && count($coff32) > 0)) {
			die('Invalid selection please correct.');
		}
		// See if such record already exists; enrolled first.
		$addReqs = AddHistory::find()
			->where([
				'ah_std_relId' => $std->id,
				'ah_course_relId' => $addCourse,
				'ah_approved' => 0
			])
			->all();

		$model->ah_course_relId = intval($addCourse);
		$model->ah_addWithGroup_relId = $addWithGroup == 0 ? die('Die: ' . $addWithGroup) : intval($addWithGroup);
		$model->ah_std_relId = $std->id;
		$model->ah_sem_relId = $this->semId;
		$model->ah_acadYear_relId = $this->acadYearId;
		$model->ah_srd2_relId = NULL;
		$model->ah_approved_by = NULL;
		$model->ah_approved_at = NULL;
		$model->ah_coff_relId =  $addWithCoff;

		$model->ah_created_by = $model->ah_updated_by = Yii::$app->user->identity->id;
		$model->ah_created_at = $model->ah_updated_at = date('Y-m-d h:i:s');

		// var_dump($model);die();
		if ($model->save()) {
			echo 1;
			die();
		} else {
			print_r($model->errors);
			die();
		}
	}

	protected function toNumber($rrr)
	{
		return intval($rrr == 'I' ? 1 : ($rrr == 'II' ? 2 : ($rrr == 'III' ? 3 : ($rrr == 'IV' ? 4 : 5))));
	}

	// returns the list of depts that offer the selected course in this semester.

	public function actionGetdepts()
	{
		if (isset($_POST['courseId']) && !empty($_POST['courseId'])) {
			$this->__constructor();

			$courseId = $_POST['courseId'];
			// $depts = CourseOffering2::find()->innerJoinWith('coffCocatRel','course_offering.coff_cocat_relId = course_catalog.id',
			//   'course_offering.coff_course_relId =course_catalog.cocat_course_relId')
			// ->andWhere(['course_catalog.cocat_sem_relId'=>$this->semId,])
			// ->andWhere(['course_offering.coff_course_relId'=>$courseId])
			// ->select(['course_offering.*', 'course_catalog.cocat_dept_relId'])->distinct()->all();

			$coff32 = CourseOffering2Detail::find()
				->where([
					'course_offering2_detail.coff2det_course_relId' => $courseId
				])
				->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
				->andWhere([
					'coff2_acadYear_relId' => $this->acadYearId,
					'coff2_sem_relId' => $this->semId,
				])
				->innerJoin('course_catalog', 'course_catalog.id = course_offering2_detail.coff2det_cocat_relId')
				->andWhere(['OR', ['course_catalog.cocat_sem_relId' => $this->semId], ['course_catalog.cocat_sem_relId' => NULL]])
				->with(['coff2detCoff2Rel', 'coff2detCocatRel'])
				->groupBy('course_catalog.cocat_dept_relId')
				->all();

			//var_dump($coff32);die();


			if ($coff32) {
				foreach ($coff32 as $dept) {
					// echo "<br>Det. ".\backend\models\Department::findOne(['id'=>$dept['coffCocatRel']['cocat_dept_relId']])->dept_name.' Course: '.\backend\models\Course::findOne(['id'=>$dept['coff_course_relId']])->course_title ;
					// $group = \backend\models\WGroupSetup::findOne(['id'=>$dept['coff2detCoff2Rel']['coff2_group_relId']]);
					// $clslvl = \backend\models\ClassLevel::findOne(['id'=>$group->gs_clslvl_relId]);
					// $title = \backend\models\WGroup::findOne(['id'=>$group->gs_group_relId])->group_name;
					// $title .= ' ['.$clslvl->clslvl_num.' -yr] ';
					// $inst = \backend\models\Instructor::findOne(['id'=>$dept['coff2det_inst_relId']]);
					// $user = \backend\models\User::findOne(['id'=>$inst->inst_user_relId]);

					// $title .= ' Instructor => '.$user->first_name.' '.$user->middle_name;

					echo "<option value='" . $dept['coff2detCocatRel']['cocat_dept_relId'] . "'>" . \backend\models\Department::findOne(['id' => $dept['coff2detCocatRel']['cocat_dept_relId']])->dept_name . "</option>";
				}
			} else {
				echo "<option value='' title='No dept. offers tthis course in this semester.'>-</option>";
				// echo "<p class='bg-danger'> No dept offer this course in this semester.</p>";
			}
		} else {
			echo "<p class='bg-danger'>Uknown Course</p>";
		}
	}

	// private function checkIfEnrolledBefore($courseId,$stdId){
	//   $exists = \frontend\models\SemesterRegistration2Detail::find()
	//   ->innerJoin('semester_registration_2','semester_registration_2.id  = semester_registration_2_detail.srd2_semreg_relId')
	//   ->innerJoin('grade_scale','grade_scale.id = semester_registration_2_detail.srd2_grade_relId')
	//   ->where(['srd2_course_relId'=>$courseId])
	//   ->andWhere(['semreg_std_relId'=>$stdId])
	//   ->andWhere(['>=','srd2_corptd_flag',2])
	//   ->andWhere(['grade_scale.gs_ltr_grade'=>['A+','A']])
	// }

	public function actionGetgroups()
	{
		if (isset($_POST['deptId']) && !empty($_POST['deptId']) && isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])) {
			$this->__constructor();

			$group = '';
			$clslvlId = $_POST['clslvlId'];
			$deptId = $_POST['deptId'];
			$courseId = $_POST['courseId'];

			$courseObject = Course::findOne(['id' => $courseId]);
			if (!($courseObject)) {
				die('Invalid course detail.');
			}
			$std = Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);

			$gset = WGroupSetup::find()->where([
				'gs_adm_relId' => $std->std_admission_relId,
				'gs_prog_relId' => $std->std_prog_relId,
				'gs_dept_relId' => $deptId,
				'gs_clslvl_relId' => $clslvlId,
				'gs_acadYear_relId' => $this->acadYearId,
				'gs_sem_relId' => $this->semId,
			])->all();

			if ($gset) {
				foreach ($gset as $g) {

					// check if the course is offered for the group
					$offerered = $this->checkOffered($g, $courseObject);

					if (!($offerered && count($offerered) > 0)) {
						continue;
					}
					// getStudentGroupCourses($gsetId,$studentId,$semesterId,$clslvlId)

					// get total students enrolled this course.
					$enrolledStudents = CCF::getTotalEnrolledStudents($g, $courseObject);
					// check for the max of the group size here
					$enrolledStudents = $enrolledStudents->all();
					if ($enrolledStudents && count($enrolledStudents) > 0) {
					} else {
						continue;
					}
					$Instructor = $enrolledStudents[0]['semesterRegistration2Detail']['coff2det_inst_relId'];
					$iUser = \backend\models\Instructor::findOne(['id' => $Instructor])->inst_user_relId;
					$user = \backend\models\User::findOne(['id' => $iUser]);
					$title = $g['gs_size'];
					$rr = count($enrolledStudents);
					$title .= '/' . $rr;
					$title .= ' [INST => ' . $user->first_name . ' ' . $user->middle_name . ' ] ';
					$group .= "<option data-coff='{$enrolledStudents[0]['semesterRegistration2Detail']['srd2_coff_relId']}' title='" . $title . "' value='" . $g['id'] . "'>" . \backend\models\WGroup::findOne(['id' => $g['gs_group_relId']])->group_name . "</option>";
				}
			} else {
				$group = "<option value='' title=' No active group taking this course is found in this dept, choose another.'>-</option>";
			}
			echo $group;
			die();
		} else {
			echo "Error selection.";
		}
	}

	// check if the course is offered for the group in this semseter.
	protected function checkOffered($gsetObject, $courseObject)
	{
		$this->__constructor();
		$offering = \backend\models\CourseOffering2::find()
			->where([
				'coff2_acadYear_relId' => $this->acadYearId,
				'coff2_sem_relId' => $this->semId,
				'coff2_group_relId' => $gsetObject->id,
			])
			->innerJoin('course_offering2_detail', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
			->andWhere(['coff2det_course_relId' => $courseObject->id])
			->all();

		return $offering;
	}


	public function actionGetclslvl()
	{
		if (isset($_POST['deptId']) && !empty($_POST['deptId']) && isset($_POST['courseId']) && !empty($_POST['courseId'])) {
			$this->__constructor();
			$std = Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);

			$clslvl = '';
			$group = "<option data-coff='' value='' title='select Group'> - </option>";

			$courseId = $_POST['courseId'];
			$deptId = $_POST['deptId'];

			$cocats = CourseCatalog::find()
				->where([
					'cocat_course_relId' => $courseId,
					'cocat_dept_relId' => $deptId
				])->all();

			//var_dump($cocats);die();
			$clslvls = [];
			if ($cocats && count($cocats) > 0) {
				foreach ($cocats as $key) {
					if (!isset($key->cocat_clslvl_relId)) {
						continue;
					}
					$clslvl .= "<option value='{$key->cocat_clslvl_relId}'>{$key->cocatClslvlRel->clslvl_name}</option>";
					array_push($clslvls, $key['cocat_clslvl_relId']);
				}

				//$clslvl .= "<option value='5'>check</option>";
				// Get groups 
				$gset = WGroupSetup::find()->where([
					'gs_adm_relId' => $std->std_admission_relId,
					'gs_prog_relId' => $std->std_prog_relId,
					'gs_dept_relId' => $deptId,
					'gs_clslvl_relId' => $clslvls,
					'gs_acadYear_relId' => $this->acadYearId,
					'gs_sem_relId' => $this->semId,
				])->all();

				$courseObject = \backend\models\Course::findOne(['id' => $courseId]);
				if (!($courseObject)) {
					die('Invalid course detail.');
				}
				if ($gset) {
					foreach ($gset as $g) {
						// check if the course is offered for the group
						$offerered = $this->checkOffered($g, $courseObject);

						if (!($offerered && count($offerered) > 0)) {
							continue;
						}

						// get total students enrolled this course.
						$enrolledStudents = CCF::getTotalEnrolledStudents($g, $courseObject);
						// check for the max of the group size here



						$enrolledStudents = $enrolledStudents->all();

						$title = $g['gs_size'];
						$rr = count($enrolledStudents);
						if ($g['gs_size'] <= $rr) {
							// cann't add with this group because group count exceeds.
							continue;
						}
						$title .= '/' . $rr;


						$coff3232 = CCF::getCourseInstructorForGroup($g->id, $courseObject->id);

						if ($coff3232) {
							$Instructor = $coff3232['coff2det_inst_relId'];
							if (is_null($Instructor)) {
								$title .= " [ Instructor Not Assigned Yet.]";
							} else {
								$iUser = \backend\models\Instructor::findOne(['id' => $Instructor])->inst_user_relId;
								$user = \backend\models\User::findOne(['id' => $iUser]);
								$title .= ' [ INST => ' . $user->first_name . ' ' . $user->middle_name . ' ]';
							}
						}

						$group .= "<option data-coff='{$coff3232['id']}' title='" . $title . "' value='" . $g['id'] . "'>" . \backend\models\WGroup::findOne(['id' => $g['gs_group_relId']])->group_name . "</option>";
					}
				} else {
					$group = "<option value='' title=' No active group taking this course is found in this dept, choose another.'>-</option>";
				}
			} else {
				$clslvl = "<option value='' title=' This course is not offered to any batch in this department, choose another dept.'>-</option>";
			}

			echo json_encode(['clslvl' => $clslvl, 'group' => $group]);
			die();
		} else {
			echo "<p class='bg-danger'>Uknown query, please correct.</p>";
		}
		die();
	}

	protected function currentStudentGroup($stdObject)
	{
		$this->__constructor();
		$gset = \backend\models\WGroupSetup::find()
			->where([
				'gs_acadYear_relId' => $this->acadYearId,
				'gs_sem_relId' => $this->semId,
				'gs_adm_relId' => $stdObject->std_admission_relId,
				'gs_prog_relId' => $stdObject->std_prog_relId,
			])
			->innerJoin('w_group_assignment', 'w_group_setup.id = w_group_assignment.gas_gset_relId')
			->andWhere(['w_group_assignment.gas_std_relId' => $stdObject->id])
			->with('wGroupAssignments')
			->one();
		return $gset;
	}

	protected function varDumpTable($CourseCatalogObject)
	{
		$CourseCatalogObject = $CourseCatalogObject->all();


		if ($CourseCatalogObject && count($CourseCatalogObject) > 0) {
			// echo "<p class='bg-success'>Your enrolled courses.</p>";
			$tab = "<table class='table table-striped table-condensed table-sm table-bordered'>";
			$tab .= "<caption><p class='bg-success'> Requested <i class='bg-danger'>" . count($CourseCatalogObject) . "</i> course(s) to add.</p></caption>";
			$tab .= "<thead>
          <th>#</th>
          <th>Course</th>
          <th>Aprvd.</th>
          </thead><tbody>";
			$count = 0;
			foreach ($CourseCatalogObject as $e) {
				// $student = \backend\models\Student::findOne(['id'=>$std->gas_std_relId]);

				$course = \backend\models\Course::findOne(['id' => $e['cocat_course_relId']]);
				$tab .= "<tr>
          <td>" . ++$count . "</td>
          <td>" . $course->course_title . "</td>
          </tr>";
			}
		} else {
			return NULL;
		}
		return $tab;
	}

	protected function checkStudentSemsterStatus($stdId)
	{
		$student = Student::findOne(['id' => $stdId]);

		// Valid semester statuses. 
		$status = $student->std_semester_status;
		$validStatus = ['-', 'ACTIVE', 'PASS', 'WARNING', 'PROBATION 1', 'PROBATION 2'];  //Hard 

		if (!in_array(strtoupper($student->std_semester_status), $validStatus)) {
			echo "<p class='bg-danger'>You can not register course for this semester. Yor semester status is <b>{$status}</b></p>";

			die();
		}
	}

	protected function checkSelfSponsoredPaymentStatus($stdId, $pymntRsnId = NULL)
	{
		$this->__constructor();

		$paid = NULL;
		if (is_null($pymntRsnId)) {
			$paid = \backend\models\SelfSponsoredPaymentHistory::findOne([
				'ssph_std_relId' => $stdId,
				'ssph_approved' => 1,
				'ssph_registrarApproved' => 1,
				'ssph_acadYear_relId' => $this->acadYearId,
				'ssph_sem_relId' => $this->semId,
			]);

			if ($paid) {
				# code...
			} else {
				$paid = \backend\models\SelfSponsoredPaymentHistory::find()->where([
					'ssph_std_relId' => $stdId,
					'ssph_acadYear_relId' => $this->acadYearId,
					'ssph_sem_relId' => $this->semId,
				])->one();
			}
		} else {
			$paid = \backend\models\SelfSponsoredPaymentHistory::findOne([
				'ssph_std_relId' => $stdId,
				'ssph_approved' => 1,
				'ssph_registrarApproved' => 1,
				'ssph_acadYear_relId' => $this->acadYearId,
				'ssph_sem_relId' => $this->semId,
				'ssph_pymntRsn_relId' => $pymntRsnId,
			]);
			if ($paid) {
			} else {
				$paid = \backend\models\SelfSponsoredPaymentHistory::find()->where([
					'ssph_std_relId' => $stdId,
					'ssph_acadYear_relId' => $this->acadYearId,
					'ssph_sem_relId' => $this->semId,
					'ssph_pymntRsn_relId' => $pymntRsnId,
				])->one();
			}
		}

		if ($paid) {

			if (is_null($paid->ssph_approved)) {
				echo "<p class='bg-danger'>Pending approval from CEP Clerk.</p>";
			} else if ($paid->ssph_approved == 1) {
				if (is_null($paid->ssph_registrarApproved)) {
					echo "<p class='bg-danger'>Your payment request is NOT APPROVED YET by registrar</p>";
				} else if ($paid->ssph_registrarApproved == 1) {
					return TRUE;
				} else {
					echo "<p class='bg-danger'>Your payment request is rejected by registrar. </p>";
				}
			} else {
				echo "<p class='bg-danger'>Your payment request is rejected by CEP Clerk. (ERR: 505)</p>";
			}
		} else {
			echo "<p class='bg-danger'>You haven't made payment of this semester, If you did and this is error message please contact CEP for checking. Else complete the payment process first before the registration period closes.</p>";
		}
		die();
	}

	private function checkIfAttended5II($stdObj)
	{ //check if completed or attended [5,[II]]
		// get student group history of [5,[II]];
		$cls5Ob = \backend\models\ClassLevel::findOne(['clslvl_num' => 5]);
		$semIIOb = \backend\models\Semester::findOne(['semester_name' => 'II']);

		$gsHistory = CCF::getStudentGroupHistoryPublic($cls5Ob->id, $semIIOb->id, $stdObj->id);
		return ($gsHistory && count($gsHistory) > 0);
	}

	private function GCStudent($gset)
	{ //check if attnded [5,[I,II]] before
		if (strtoupper($gset->gsProgRel->program_name) == 'MASTERS') {
			return $gset->gsClslvlRel->clslvl_num * 2 == 4;
		} else if (strtoupper($gset->gsProgRel->program_name) == 'DEGREE') {
			return $gset->gsClslvlRel->clslvl_num * 2 == 10;
		} else {
			return false;
		}
	}

	private function checkIfMaster2II($gsetObj, $stdObject)
	{
		// check if is 2,I
		// return "Going...";
		if (strtoupper($gsetObj->gsProgRel->program_name) == 'MASTERS') {
			if ($gsetObj->gsClslvlRel->clslvl_num == 2 && $gsetObj->gsSemRel->semester_name == 'II') {

				// NO choice am gonna to use hard code here::
				// 'cocat_adm_relId'=>$gsetObj->gs_adm_relId,
				// 'cocat_adm_relId'=>$gsetObj->gs_adm_relId,
				$thesisICourseId = \backend\models\CourseCatalog::find()
					->innerJoin('course', 'course.id = course_catalog.cocat_course_relId')
					->where(['course.course_title' => 'Thesis Phase I'])
					->andWhere([
						'cocat_admission_relId' => $gsetObj->gs_adm_relId,
						'cocat_program_relId' => $gsetObj->gs_prog_relId,
						'cocat_dept_relId' => $gsetObj->gs_dept_relId,
					])
					->andWhere([
						'cocat_courseProject' => 1
					])
					->one();
				// check if enrolled Thesis phase I and has valid pass status

				if ($thesisICourseId) {
					// var_dump($thesisICourseId);die();
				} else {
					return "No Course titled 'Thesis Phase I' NOT found in your course matrix";
				}
				$semregDetObj = \frontend\models\SemesterRegistration2Detail::find()
					->innerJoin('semester_registration_2', 'semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
					->where([
						'semreg_std_relId' => $stdObject->id,
					])
					->andWhere([
						'srd2_course_relId' => $thesisICourseId->cocat_course_relId,
					])
					->one();
				if ($semregDetObj && count($semregDetObj) > 0) {
					if ($semregDetObj->srd2GradeRel->gs_pass_status == 1) { //THESIS_PHASE_I_GRADE status is pass
						echo "<p class='bg-success'>Grade obtained on {$semregDetObj->srd2CourseRel->course_title} : {$semregDetObj->srd2GradeRel->gs_ltr_grade}</p>";
						return TRUE;
						// die("Letter Grade: ".);
					} else {
						return "Minimum requirement for semester registration is NOT satisfied, Your THESIS_PHASE_I_GRADE is {$semregDetObj->srd2GradeRel->gs_ltr_grade}";
					}
				} else {
					die("<p class='bg-danger'>Enrolled 'Thesis Phase I' ? , RECORD_COULD_NOT_BE_FOUND</p>");
				}
			}
			return TRUE;
		} else {
			return TRUE; //for others than MASTERS - [2,I].
		}
	}

	private function checkIfMaster2I($gsetObj, $stdObject)
	{
		// check if is 2,I
		if (strtoupper($gsetObj->gsProgRel->program_name) == 'MASTERS') {
			if ($gsetObj->gsClslvlRel->clslvl_num == 2 && $gsetObj->gsSemRel->semester_name == 'I') {
				// get semetser grade of the student on 1,II
				$pClslvlObj = \backend\models\ClassLevel::findOne(['clslvl_program_relId' => $gsetObj->gs_prog_relId, 'clslvl_num' => 1]);
				$pSemObj = \backend\models\Semester::findOne(['semester_name' => 'II']);

				$Gset1II = CCF::getStudentGroupHistoryPublic($pClslvlObj->id, $pSemObj->id, $stdObject->id);
				CCF::getSemesterGrade($pClslvlObj->id, $pSemObj->id, $stdObject->id);

				$semregObj = \frontend\models\SemesterRegistration2::find()
					->where([
						'semreg_std_relId' => $stdObject->id,
						'semreg_semester_relId' => $pSemObj->id,
						'semreg_clslvl_relId' => $pClslvlObj->id,
					])
					->one();
				if ($semregObj) {
					if ($semregObj->semreg_CGPA >= 3.00) {
						// die($semregObj->semreg_CGPA);
						return FALSE;
					} else {
						$dd = sprintf("%0.2f", round($semregObj->semreg_CGPA, 2, PHP_ROUND_HALF_UP));
						return "Minimum requirement for semester registration is NOT satisfied, Your CGPA is {$dd}";
					}
				} else {
					die("<p class='bg-danger'>Invalid previous semeseter status, RECORD_COULD_NOT_BE_FOUND</p>");
				}
			}
			return TRUE;
		} else {
			return TRUE; //for others than MASTERS - [2,I].
		}
	}

	// returns gsets that are assigned to classLevels to whom the student can add the course with.
	private function getValidClassLevelGsets($stdObj)
	{
		$this->__constructor();
		// Student can add courses with class Levels in interval    [1,stdObj->stdClslvl+1]  ;
		$gsetObj = NULL;
		if (is_object($stdObj)) {
			return \backend\models\WGroupSetup::find()
				->where([
					'gs_acadYear_relId' => $this->acadYearId,
					'gs_sem_relId' => $this->semId,
					'gs_adm_relId' => $stdObj->std_admission_relId,
					'gs_prog_relId' => $stdObj->std_prog_relId,
				])
				->all();
		}
		return $gsetObj;
	}
	// get-course-for-adding
	public function actionGetCourseForAdding()
	{

		$coffIdsToAdd = [];
		if (!Yii::$app->user->can('Student')) {
			die('Only Students are allowed for this activity');
		}
		$msg = '';
		$this->__constructor();
		// 1st : courses to be added should be below student's current class level.
		// CourseOffering->coff_clslvl < $std->std_clslvl
		$userId = Yii::$app->user->identity->id;

		$std = Student::findOne(['std_user_relId' => $userId]);
		$this->checkStudentSemsterStatus($std->id);
		$gset = $this->currentStudentGroup($std);
		if (!($gset)) {
			die('<p class="bg-danger">Group not setted yet, please wait.</p>');
		}

		// Student should enroll at atleast one course of the semsetr
		$enrolledCheck = \frontend\models\SemesterRegistration2Detail::find()
			->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
			->where([
				'semreg_acadYear_relId' => $this->acadYearId,
				'semreg_semester_relId' => $this->semId
			])
			->andWhere(['semreg_std_relId' => $std->id])
			->one();
		if ($enrolledCheck) {
			# code...
		} else {
			if ($this->checkIfAttended5II($std) || strtoupper($gset->gsProgRel->program_name) == 'MASTERS') {
			} else {
				die("<p class='bg-danger'>You have to enroll at least one course of the semsetr.</p>");
			}
		}
		$actionName = 'Course add';
		$action = \backend\models\AcademicCalanderAction::findOne(['aca_name' => $actionName]);
		if ($action) {
			$schedule = CCF::getScheduleForAction($gset, $action->id);
			if (!($schedule && count($schedule))) {
				die('Schedule is not open for course adding.');
			}
		} else {
			die("Unregistered action {$actionName}");
		}

		$feeStudents = [];
		$selfSponsored = \backend\models\ScholarshipType::find()->where(['schType_pay' => 1])->all();
		if ($selfSponsored) {
			foreach ($selfSponsored as $key) {
				$feeStudents[] = $key->id;
			}
		}
		if (in_array($std->std_schlrshpType_relId, $feeStudents)) {
			$pymntRsn = \backend\models\PaymentReason::findOne(['pr_desc' => 'self-sponsored semester payment']);
			$this->checkSelfSponsoredPaymentStatus($std->id, $pymntRsn->id);
		}


		$sgrades = null;
		if (isset($std->std_prog_relId)) {
			$program = \backend\models\Program::findOne($std->std_prog_relId);
			if (strtoupper($program->program_name) == 'MASTERS') {
				$sgrades = \backend\models\GradeScale::find()->where([
					'gs_ltr_grade' => ['A', 'A+', 'B', 'B+']
				])->all();
			} else {
				//check if graduating classs
				if ($this->GCStudent($gset)) { //GC students can add D courses
					$sgrades = \backend\models\GradeScale::find()->where([
						'gs_ltr_grade' => ['C', 'C+', 'A', 'A+', 'B', 'B+']
					])->all();
				} else {
					$sgrades = \backend\models\GradeScale::find()->where([
						'gs_ltr_grade' => ['C', 'C+', 'D', 'A', 'A+', 'B', 'B+']
					])->all();
				}
			}
		}

		$validGrades = [];
		if ($sgrades) {
			foreach ($sgrades as $key) {
				$validGrades[] = $key->id;
			}
		}

		$reAddableCoursesArray = [];
		// from semester registration, get those courses whose grade is out of [$validGrades]
		$repeatedCourse = [];
		$reAddableCourses = \frontend\models\SemesterRegistration2Detail::find()
			->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
			->where(['NOT IN', 'srd2_grade_relId', $validGrades])
			->andWhere(['semreg_std_relId' => $std->id])
			->andWhere(['IS NOT', 'srd2_grade_relId', NULL])
			->all();

		if ($reAddableCourses) {
			foreach ($reAddableCourses as $RAkey) {
				if ($RAkey->srd2_corptd_flag == 1) {
					$repeatedCourse[] = $RAkey->srd2_course_relId;
				}
				$reAddableCoursesArray[] = $RAkey->srd2_course_relId;
			}
		}

		// $reAddableCoursesArray = array_diff($reAddableCoursesArray, $repeatedCourse);
		// take the reverse - to get all successfully completed courses.
		$sCompletedCourses = \frontend\models\SemesterRegistration2Detail::find()
			->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
			->where(['NOT IN', 'srd2_course_relId', $reAddableCoursesArray])
			->andWhere(['semreg_std_relId' => $std->id])
			->andWhere(['srd2_corptd_flag' => 0])
			->all();

		$sCoursesArray = [];
		if ($sCompletedCourses) {
			foreach ($sCompletedCourses as $SCkey) {
				$sCoursesArray[] = $SCkey->srd2_course_relId;
			}
		}


		// alreday requested courses not allowed for re-request.
		// approval pending or approved courses should not be displayed again
		$addReqs = AddHistory::find()
			->where(['ah_std_relId' => $std->id])
			->andWhere(['OR', ['ah_approved' => NULL], ['ah_approved' => 1]])
			->all();

		$arcids = [];
		if ($addReqs) {
			foreach ($addReqs as $ARkey) {
				$arcids[] = $ARkey['ah_course_relId'];
				$sCoursesArray[] = $ARkey->ah_course_relId;
			}
		}
		// // get those course in his/her catagory that are out of SCourses.

		// $reAddableCatagoryCourses = \backend\models\CourseCatalog::find()
		//   ->where()
		//   ->andWhere()
		//   ->

		$sCoursesArray = array_unique($sCoursesArray);

		// var_dump($sCoursesArray);
		// die();
		// Get valid Group Setups for addingWith...
		$gsets = $this->getValidClassLevelGsets($std);
		$gsetsArray = [];
		if ($gsets) {
			foreach ($gsets as $GSkey) {
				$gsetsArray[] = $GSkey->id;
			}
		}
		// ->andWhere(['IS NOT','coff2det_inst_relId',NULL])
		$offeredReAddableCourses = \backend\models\CourseOffering2Detail::find()
			->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
			->innerJoin('course_catalog', 'course_catalog.id = course_offering2_detail.coff2det_cocat_relId')
			->where([
				'cocat_admission_relId' => $std->std_admission_relId,
				'cocat_program_relId' => $std->std_prog_relId,
			])
			->andWhere(['NOT IN', 'cocat_course_relId', $sCoursesArray])
			->andWhere(['NOT IN', 'cocat_course_relId', $repeatedCourse])
			->andWhere([
				'coff2_group_relId' => $gsetsArray,
				'coff2det_registrarApproved' => 1,
			])
			->groupBy('coff2det_course_relId')
			->orderBy('coff2det_course_relId')
			->all();

		// if the course added result is greater than the max credit allowed, pass the course.
		$coursesEnrolled = CCF::getStudentGroupCoursesP($gset->id, $std->id, $this->semId, $gset->gs_clslvl_relId);

		$creditsCollected = 0;
		$addCrdtSum = 0;
		if ($coursesEnrolled) {
			// Sum up the credits collected in this semsetr.
			foreach ($coursesEnrolled as $key) {
				$creditsCollected += $key->srd2CourseRel->course_crdthr;
			}
		} else {
			$creditsCollected = 0;
		}

		$maxAllowedCedit = 0; //default maxCredit for semester registration is 19.

		$maxAllowedObject = CCF::getMaxAllowedCreditHrPerSemester($gset);

		if ($maxAllowedObject) {
			$maxAllowedCedit = $maxAllowedObject->src_max_crdthr;
		}

		//var_dump($creditsCollected);die();

		$count = 0;
		$ncount = 0;
		$tab = '';
		if ($addReqs) {
			// echo "<p class='bg-success'>Your enrolled courses.</p>";
			$tab .= "<table class='table table-striped table-condensed table-sm table-bordered'>";
			$tab .= "<caption><p class='bg-success'> Requested <i class='bg-danger'>" . count($addReqs) . "</i> course(s) to add.</p></caption>";
			$tab .= "<thead>
          <th>#</th>
          <th>Course</th>
          <th>Aprvd.</th>
          <th>Option</th>
          </thead><tbody>";
			foreach ($addReqs as $e) {
				$course = \backend\models\Course::findOne(['id' => $e['ah_course_relId']]);

				$btn = "-";
				if (is_null($e['ah_approved'])) {
					$addCrdtSum += $course->course_crdthr;
					$btn = "<button class='btn btn-warning btn-xs' onclick='deleteAddRequest({$e->id})'>Cancel</button>";
				}
				$gr = is_null($e['ah_approved']) ? 'Pending' : ($e['ah_approved'] == 1 ? 'Approved' : 'Rejected');

				$tab .= "<tr>
          <td>" . ++$count . "</td>
          <td>" . $course->course_title . "</td>
          <td>" . $gr . "</td>
          <td>{$btn}</td>
          </tr>";
			}
		}

		$aCourses = '';
		$message2 = NULL;
		$modelCocat = new \backend\models\CourseCatalog();
		$aCourses .= '<option value=""> </option>';
		// $offeredReAddableCourses as $COFFkey
		if ($offeredReAddableCourses and count($offeredReAddableCourses) > 0) {
			$tab .= "<table class='table table-condensed table-sm table-bordered'>";
			$tab .= "<caption><p class='bg-warning'> Courses not enrolled yet (<i class='bg-danger'>" . count($offeredReAddableCourses) . "</i>)</p></caption>";
			$tab .= "<thead>
      <th>#</th>
      <th>Course</th>
      <th>Opt.</th>
      </thead><tbody>";

			// echo "Offered Courses:";
			foreach ($offeredReAddableCourses as $COFFkey) {
				$pass = FALSE;
				$message = '';
				$course2 = $COFFkey->coff2detCourseRel;

				if ($this->checkHaveClearedFGrade($course2->id, $std->id, $validGrades)) {
					continue;
				}

				// if course is NOT project Course and Instructor is NOT assigned for it, Leave it.

				if (!$modelCocat->isProjectCourses($gset, $course2->id) && is_null($COFFkey->coff2det_inst_relId)) {
					continue;
				}

				// adding or enrolling THESIS PHASE II is allowed only if the students is enrolled THESIS PHASE I
				if (strtoupper($course2->course_title) == 'THESIS PHASE II') {
					$eligible2 = $this->checkIfMaster2II($gset, $std, $course2->id);
					if (is_bool($eligible2) && $eligible2 == TRUE) { //check foe Thesis II
						// check if eligible for Thesis Phase I,
					} else {
						continue;
					}
				}

				// Thesis course adding is allowed only when student have CGPA > 3.00
				if (strtoupper($course2->course_title) == 'THESIS PHASE I') {
					$eligible = $this->checkIfMaster2I($gset, $std, $course2->id);
					if (is_bool($eligible) && $eligible == TRUE) { //check for Thesis I
						// check if eligible for Thesis Phase II,
					} else {
						continue;
					}
				}
				// echo "<p>{$course2->course_title} - {$course2->course_code}</p>";
				// continue;

				if (!is_null($std->std_spec_relId)) {
					$cocat = CCF::getCatalogCourse($gset)->andWhere(['cocat_course_relId' => $COFFkey->coff2det_course_relId])->one();
					if ($cocat && count($cocat) > 0) {
					} else {
						continue;
					}
					if (!is_null($cocat->cocat_spec_relId) && $std->std_spec_relId != $cocat->cocat_spec_relId) {
						continue;
					}
				}

				$precModel = new PreRequisitController();
				$prec = $precModel->safeOfPrerequisit($course2, $std, $gset);
				$message .= $prec[1];
				$pass = $prec[0];

				if ($pass) {

					$enn = $this->checkEnrolled($course2->id, $std->id);

					// check maximum credit hour
					if ($enn && count($enn) > 0) {
						//continue;
					} else {
						// Course's credit + totatCreditColllected exceeds the maxCredutHr, pass the course.
						if (($creditsCollected + $addCrdtSum + $course2->course_crdthr) > $maxAllowedCedit) {
							$message2 = "You have more courses to add but no more free credit hour is available. Max. Allowed CrdtHr Is {$maxAllowedCedit}";
							continue;
						} else {
							// Count times how much the course added by this student.
							if ($this->countTimesAdded($course2->id, $std->id) >= 2) {
								// Studnt have added the course more than two times.
							} else {
								$aCourses .= "<option value='{$course2->id}'>" . $course2->course_code . ' => ' . $course2->course_title . "</option>";
							}
						}
					}
					// echo "<button class='btn btn-sm btn-success' onclick='addCourse(". $e['coff_course_relId'] .",\"". $course2->course_title ."\")' title='Add this course.'>Add</button>";
				} else {
					$tab .= "<tr class='bg-warning' style='font-style:italic; ' title='Course not enrolled Yet.'>
            <td>" . ++$ncount . "</td>
            <td>" . $course2->course_code . ' => ' . $course2->course_title . "</td>
            <td>";
					$tab .= $message;
				}
				$tab .= "</td>
        </tr>";
			}
		}

		$tab .= "</tbody></table>";

		echo json_encode(['table' => $tab, 'courseDrop' => $aCourses, 'message' => $msg, 'creditsCollected' => $creditsCollected, 'maxAllowedCedit' => $maxAllowedCedit, 'message2' => $message2]);
	}

	// check if the student is cleared F grade.
	private function checkHaveClearedFGrade($courseId, $stdId, $validGrades)
	{
		// return FALSE;
		return SemesterRegistration2Detail::find()
			->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
			->where(['semreg_std_relId' => $stdId])
			->andWhere(['srd2_course_relId' => $courseId])
			->andWhere(['srd2_grade_relId' => $validGrades])
			->count() > 0;
	}

	// return the count how much time the course added by the student.
	protected function countTimesAdded($courseId, $stdId)
	{
		$count = AddHistory::find()
			->where([
				'ah_course_relId' => $courseId,
				'ah_std_relId' => $stdId
			])
			->count();
		return $count;
	}

	public function actionDeleteAddRequest()
	{
		$id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']) : die("Invlaid Query");
		$validForRemoving = AddHistory::find()
			->where(['id' => $id,])
			->andWhere(['OR', ['ah_approved' => null], ['ah_approved' => 0]])
			->one();
		if ($validForRemoving) {
			return AddHistory::findOne($id)->delete();
		}
		echo "Invalid Request, Please Refine it.";
		die();
	}

	public function actionRequestReport()
	{
		if (Yii::$app->user->can('Student')) {
			// get requests made this student.
			$std = Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
			$this->__constructor();
			$requests = AddHistory::find()
				->where(['ah_acadYear_relId' => $this->acadYearId, 'ah_sem_relId' => $this->semId, 'ah_std_relId' => $std->id])
				->orderBy('ah_approved')
				->all();

			$tab = '';
			$count =  0;
			if ($requests) {
				$tab .= "<table class='table table-striped table-condensed table-sm table-bordered'>";
				$tab .= "<caption><p class='bg-success'> Requested <i class='bg-danger'>" . count($requests) . "</i> course(s) to add.</p></caption>";
				$tab .= "<thead>
          <th>#</th>
          <th>Course</th>
          <th>Aprvd.</th>
          </thead><tbody>";
				foreach ($requests as $e) {
					$course = Course::findOne(['id' => $e['ah_course_relId']]);

					$gr = is_null($e['ah_approved']) ? 'Pending' : ($e['ah_approved'] == 1 ? 'Approved' : 'Dis-approved.');

					$tab .= "<tr>
              <td>" . ++$count . "</td>
              <td>" . $course->course_title . "</td>
              <td>" . $gr . "</td>
              </tr>";
				}
				echo "</tbody></table>";
			}
		}
	}

	protected function checkEnrolled($courseId, $studentId)
	{
		$semreg = SemesterRegistration2Detail::find()
			->where(['semreg_std_relId' => $studentId])
			->innerJoin('semester_registration_2', 'semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
			->andWhere(['semester_registration_2_detail.srd2_course_relId' => $courseId])
			->andWhere(['semester_registration_2_detail.srd2_corptd_flag' => 1])
			->andWhere(['IS NOT', 'semester_registration_2_detail.srd2_grade_relId', NULL])
			->all();

		return $semreg;
	}
}
