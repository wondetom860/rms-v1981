<?php

namespace frontend\controllers;

use yii;
use backend\models\Course;
use frontend\models\SemesterRegistration2;
use frontend\models\SemesterRegistration2Detail;
use yii\filters\VerbFilter;
use yii\helpers\ArrayHelper;
use \common\components\CommonControllerFunctions as CCF;
use backend\models\ReassessmentHistory;
use frontend\modules\Reassessment\models\ReassessmentHistory as ModelsReassessmentHistory;
use yii\base\Exception;


/**
 * This is the model class for table "continuous_assessment".
 *
 * @property integer $id
 * @property integer $ca_coff_relId
 * @property integer $ca_semreg_relId
 * @property integer $ca_std_relId
 * @property integer $ca_assignment_relId
 * @property double $ca_markObtained
 * @property string $ca_created_at
 * @property integer $ca_created_by
 * @property integer $ca_updated_by
 * @property string $ca_updated_at
 * @property integer $ca_approved
 * @property string $ca_approved_at
 * @property integer $ca_approved_by
 *
 * @property CourseOffering2Detail $caCoffRel
 * @property SemesterRegistration2 $caSemregRel
 * @property Student $caStdRel
 * @property User $caCreatedBy
 * @property User $caUpdatedBy
 * @property User $caApprovedBy
 * @property ContAssessMarkAssignment $caAssignmentRel
 */


class ReassessmentController extends \yii\web\Controller
{
	public $acadYearId;
	public $semId;
	protected function __constructor()
	{

		$cs = CCF::currentTime();
		$this->acadYearId = $cs['Y'];
		$this->semId = $cs['S'];
	}

	/**
	 * @inheritdoc
	 */
	public function behaviors()
	{
		return [
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['POST'],
				],
			],
			'access' => [
				'class' => \yii\filters\AccessControl::class,
				// We will override the default rule config with the new AccessRule class
				'ruleConfig' => [
					'class' => \yii\filters\AccessRule::class,
				],
				'rules' => [
					[
						'actions' => [],
						'allow' => true,
						// Allow  admins to create, update, and delete, and view (index)
						'roles' => ['@'],
					],
				]
			]
		];
	}



	public function actionIndex()
	{
		return $this->render('index');
	}

	public function actionCheckPayment()
	{
		$courseId = isset($_POST['courseId']) && !empty($_POST['courseId']) ? $_POST['courseId'] : die('Uknown Course.');

		// 1.check if re-assessment payment is made for the course

		// 2.if exists
		// a.If approved, confirm and 

		// b. check 

		// 
		// echo "COURSE_ID: " .$courseId ;
	}

	public function actionRequestRegrading()
	{
		$srd2Id = Yii::$app->request->post('srd2Id');
		$modelSrd2 = SemesterRegistration2Detail::findOne($srd2Id);

		if ($modelSrd2 && !$modelSrd2->notRequested()) {
			$model = new ModelsReassessmentHistory();
			$transaction = Yii::$app->db->beginTransaction();
			$semregEntry = $modelSrd2->srd2SemregRel;
			$model->ras_std_relId = $semregEntry->semreg_std_relId;
			$model->ras_acadYear_relId = $semregEntry->semreg_acadYear_relId;
			$model->ras_sem_relId = $semregEntry->semreg_semester_relId;
			$model->ras_clslvl_relId = $semregEntry->semreg_clslvl_relId;
			$model->ras_group_relId = $modelSrd2->srd2_group_relId;
			$model->ras_srd2_relId = $srd2Id;
			// $model->ras_contAssCatag_relId = $catagoryId;
			$model->ras_bef_grade_relId = $modelSrd2->srd2_grade_relId;
			$model->ras_prevMark = $modelSrd2->srd2_total_mark;
			$model->ras_course_relId = $modelSrd2->srd2_course_relId;
			$model->ras_created_at = date('Y-m-d h:i:s');
			if ($model->save()) {
				try {
					$result = Yii::$app->db
						->createCommand()
						->update(
							'semester_registration_2_detail',
							[
								'srd2_grade_relId' => NULL,
							],
							[
								'id' => $srd2Id,
							]
						)
						->execute();
					if ($result == 1) {
						// check if the grade submission is completed or not.

						//$sr2 = \backend\models\SemesterRegistration2::findOne(['id'=>$semregEntry->id,'semreg_gradeCompleted'=>1]);
						if ($semregEntry->semreg_gradeCompleted == 1) {
							$result2 = Yii::$app->db->createCommand()
								->update(
									'semester_registration_2',
									[
										'semreg_gradeCompleted' => 0
									],
									[
										'id' => $semregEntry->id,
									]
								)->execute();

							if ($result2) {
								Yii::$app->session->setFlash('success', 'Regrading initiated succefully');
								$transaction->commit();
								return $this->redirect(['/student/view', 'id' => $semregEntry->semreg_std_relId]);
							} else {
								Yii::$app->session->setFlash('error', 'Regrading initiating failed');
								$transaction->rollBack();
								return $this->redirect(['/student/view', 'id' => $semregEntry->semreg_std_relId]);
							}
						} else {
							Yii::$app->session->setFlash('success', 'Regrade processing initiated succefully');
							$transaction->commit();
							return $this->redirect(['/student/view', 'id' => $semregEntry->semreg_std_relId]);
						}
					} else {
						Yii::$app->session->setFlash('error', 'Regrading initiating failed');
						$transaction->rollBack();
						return $this->redirect(['/student/view', 'id' => $semregEntry->semreg_std_relId]);
					}
				} catch (Exception $e) {
					Yii::$app->session->setFlash('error', 'Regrading initiating failed');
					$transaction->rollBack();
					return $this->redirect(['/student/view', 'id' => $semregEntry->semreg_std_relId]);
				}
				// echo 1;die();
			} else {
				Yii::$app->session->setFlash('error', 'Regrading initiating failed');
				$transaction->rollBack();
				return $this->redirect(['/student/view', 'id' => $semregEntry->semreg_std_relId]);
			}
		}
	}

	public function actionRequestreassessment2()
	{
		if (isset($_POST['courseId']) && !empty($_POST['courseId']) && isset($_POST['bgrdId']) && !empty($_POST['bgrdId']) && isset($_POST['srd2Id']) && !empty($_POST['srd2Id'])) {
			$courseId = $_POST['courseId'];
			$bgrdId = $_POST['bgrdId'];
			$srd2 = $_POST['srd2Id'];
			$finalMarkMax = isset($_POST['finalMarkMax']) && !empty($_POST['finalMarkMax']) ? $_POST['finalMarkMax'] : die('Final exam maximum uknown. please retry.');
			$finalMark = isset($_POST['finalMark']) && !empty($_POST['finalMark']) ? $_POST['finalMark'] : die('Final exam mark uknown. please retry.');
			$catagoryId = isset($_POST['catagoryId']) && !empty($_POST['catagoryId']) ? $_POST['catagoryId'] : die('Reassessment catagory error, Please re-try..');

			// echo $srd2;die();

			$semregDetail = \frontend\models\SemesterRegistration2Detail::findOne(['id' => intval($srd2)]);
			$semregEntry = \frontend\models\SemesterRegistration2::findOne(['id' => $semregDetail->srd2_semreg_relId]);
			$this->__constructor();
			$std = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);

			$transaction = Yii::$app->db->beginTransaction();

			$model = new \backend\models\ReassessmentHistory();
			$model->ras_std_relId = $std->id;
			$model->ras_acadYear_relId = $this->acadYearId;
			$model->ras_sem_relId = $this->semId;
			$model->ras_clslvl_relId = $std->std_clslvl_relId;
			$model->ras_group_relId = $semregDetail->srd2_group_relId;
			$model->ras_srd2_relId = $srd2;
			$model->ras_contAssCatag_relId = $catagoryId;
			$model->ras_bef_grade_relId = $bgrdId;
			$model->ras_prevMark = $finalMark;
			$model->ras_course_relId = $courseId;
			$model->ras_created_at = date('Y-m-d h:i:s');
			if ($model->save()) {
				try {
					$result = Yii::$app->db
						->createCommand()
						->update(
							'semester_registration_2_detail',
							[
								'srd2_grade_relId' => NULL,
							],
							[
								'id' => $srd2,
							]
						)
						->execute();
					if ($result == 1) {
						// check if the grade submission is completed or not.

						//$sr2 = \backend\models\SemesterRegistration2::findOne(['id'=>$semregEntry->id,'semreg_gradeCompleted'=>1]);
						if ($semregEntry->semreg_gradeCompleted == 1) {
							$result2 = Yii::$app->db->createCommand()
								->update(
									'semester_registration_2',
									[
										'semreg_gradeCompleted' => 0
									],
									[
										'id' => $semregEntry->id,
									]
								)->execute();

							if ($result2) {
								$transaction->commit();
								echo 1;
								die();
							} else {
								throw new Exception("ERROR_2 [Updating grade submission table failed.] Please retry...", 1);
							}
						} else {
							$transaction->commit();
							echo 1;
							die();
						}
					} else {
						throw new Exception("ERROR_1 [Updating Grade table failed.]", 1);
					}
				} catch (Exception $e) {
					$transaction->rollBack();
					print_r('ERROR OCCURED. ' . $e);
					die();
				}
				// echo 1;die();
			} else {
				print_r($model->errors);
				die();
			}
		} else {
			echo "No data provided.";
		}
	}
	public function actionRequestreassessment()
	{
		if (isset($_POST['courseId']) && !empty($_POST['courseId']) && isset($_POST['bgrdId']) && !empty($_POST['bgrdId']) && isset($_POST['srd2Id']) && !empty($_POST['srd2Id'])) {
			$courseId = $_POST['courseId'];
			$bgrdId = $_POST['bgrdId'];
			$srd2 = $_POST['srd2Id'];
			$finalMarkMax = isset($_POST['finalMarkMax']) && !empty($_POST['finalMarkMax']) ? $_POST['finalMarkMax'] : die('Final exam maximum uknown. please retry.');
			$finalMark = isset($_POST['finalMark']) && !empty($_POST['finalMark']) ? $_POST['finalMark'] : die('Final exam mark uknown. please retry.');
			$catagoryId = isset($_POST['catagoryId']) && !empty($_POST['catagoryId']) ? $_POST['catagoryId'] : die('Reassessment catagory error, Please re-try..');

			// echo $srd2;die();

			$semregDetail = \frontend\models\SemesterRegistration2Detail::findOne(['id' => intval($srd2)]);
			$semregEntry = \frontend\models\SemesterRegistration2::findOne(['id' => $semregDetail->srd2_semreg_relId]);
			$this->__constructor();
			$std = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);

			$transaction = Yii::$app->db->beginTransaction();

			$model = new \backend\models\ReassessmentHistory();
			$model->ras_std_relId = $std->id;
			$model->ras_acadYear_relId = $this->acadYearId;
			$model->ras_sem_relId = $this->semId;
			$model->ras_clslvl_relId = $std->std_clslvl_relId;
			$model->ras_group_relId = $semregDetail->srd2_group_relId;
			$model->ras_srd2_relId = $srd2;
			$model->ras_contAssCatag_relId = $catagoryId;
			$model->ras_bef_grade_relId = $bgrdId;
			$model->ras_prevMark = $finalMark;
			$model->ras_course_relId = $courseId;
			$model->ras_created_at = date('Y-m-d h:i:s');
			if ($model->save()) {
				try {
					$result = Yii::$app->db
						->createCommand()
						->update(
							'semester_registration_2_detail',
							[
								'srd2_grade_relId' => NULL,
							],
							[
								'id' => $srd2,
							]
						)
						->execute();
					if ($result == 1) {
						// check if the grade submission is completed or not.

						//$sr2 = \backend\models\SemesterRegistration2::findOne(['id'=>$semregEntry->id,'semreg_gradeCompleted'=>1]);
						if ($semregEntry->semreg_gradeCompleted == 1) {
							$result2 = Yii::$app->db->createCommand()
								->update(
									'semester_registration_2',
									[
										'semreg_gradeCompleted' => 0
									],
									[
										'id' => $semregEntry->id,
									]
								)->execute();

							if ($result2) {
								$transaction->commit();
								echo 1;
								die();
							} else {
								throw new Exception("ERROR_2 [Updating grade submission table failed.] Please retry...", 1);
							}
						} else {
							$transaction->commit();
							echo 1;
							die();
						}
					} else {
						throw new Exception("ERROR_1 [Updating Grade table failed.]", 1);
					}
				} catch (Exception $e) {
					$transaction->rollBack();
					print_r('ERROR OCCURED. ' . $e);
					die();
				}
				// echo 1;die();
			} else {
				print_r($model->errors);
				die();
			}
		} else {
			echo "No data provided.";
		}
	}

	public function actionRequeststatus()
	{
		$this->__constructor();

		if (Yii::$app->user->can('Student')) {
			$std = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);
			$requstedCourses = \backend\models\ReassessmentHistory::find()
				->where([
					'ras_std_relId' => $std->id,
					'ras_acadYear_relId' => $this->acadYearId,
					'ras_sem_relId' => $this->semId,
				])
				->all();

			if ($requstedCourses) {

				echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
				echo "<caption><p class='bg-success'> <i class='bg-danger'>" . count($requstedCourses) . "</i> Course(s).</p></caption>";
				echo "<thead>
			    <th>Course Title</th>
			    <th>bf. Grade</th>
			    <th>af. Grade</th>
			    <th>Status</th>
			    </thead><tbody>";
				foreach ($requstedCourses as $course) {

					$c = \backend\models\Course::findOne(['id' => $course->ras_course_relId]);
					$lg = \backend\models\GradeScale::findOne(['id' => $course->ras_bef_grade_relId]);
					$ag = is_null($course->ras_aft_grade_relId) ? '-' : \backend\models\GradeScale::findOne(['id' => $course->ras_aft_grade_relId])->gs_ltr_grade;
					$status = is_null($course->ras_status) ? 'Pending' : 'Replied';
					echo "<tr>
				    <td>" . $c->course_title . "</td>
				    <td>" . $lg->gs_ltr_grade . "</td>
				    <td>" . $ag . "</td>
				    <td>" . $status . "</td>
				    </tr>";
				}
				echo "</tbody></table>";
			} else {
				echo "<p class='bg-danger'>No request made yet.</p>";
			}
		} else if (Yii::$app->user->can('DepartmentHead') || Yii::$app->user->can('Registrar')) {
			if (Yii::$app->user->can('DepartmentHead')) {
				// Load those courses requestd for re-assessing which belog to his / her dept.

				$instDept = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id])->inst_dept_relId;

				// Requested courses of his dept.

				// $requests = \backend\models\ReassessmentHistory::find()
				// 	->where(['ras_'])
			}
		} else {
			echo "<p class='Nothing to de here.'></p>";
		}
	}

	// get student group for this semester.
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

	public function actionGetenrolledcourses()
	{
		$this->__constructor();
		$std = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);

		// check for re-assessment time schedule.
		$gset = $this->currentStudentGroup($std);
		if (!($gset)) {
			die('Group setup not made yet, please come later.');
		}

		$actionName = 'Re-assessment';
		$action = \backend\models\AcademicCalanderAction::findOne(['aca_name' => $actionName]);
		if ($action) {
			$schedule = CCF::getScheduleForAction($gset, $action->id);
			if (!($schedule)) {
				die('Schedule is not open for course re-assessment.');
			}
		} else {
			die('Unregistered action {$actionName}');
		}


		$enrolledCourses = SemesterRegistration2Detail::find()
			->innerJoin('semester_registration_2', 'semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
			->where(['semester_registration_2.semreg_acadYear_relId' => $this->acadYearId])
			->andWhere(['semester_registration_2.semreg_semester_relId' => $this->semId])
			->andWhere(['semester_registration_2.semreg_std_relId' => $std->id])
			->andWhere([' IS NOT ', 'semester_registration_2_detail.srd2_grade_relId', NULL])
			->andWhere(['semester_registration_2_detail.srd2_gradeApproved' => 1])
			->all();

		$contAssMessage = '';
		if ($enrolledCourses) {
			echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
			echo "<caption><p class='bg-success'> <i class='bg-danger'>" . count($enrolledCourses) . "</i> Course(s).</p></caption>";
			echo "<thead>
		    <th>Course Title</th>
		    <th>Grade</th>
		    <th>Final Exam</th>
		    <th>Option</th>
		    </thead><tbody>";
			foreach ($enrolledCourses as $course) {

				$c = \backend\models\Course::findOne(['id' => $course->srd2_course_relId]);
				$requstedCourses = \backend\models\ReassessmentHistory::find()
					->where([
						'ras_std_relId' => $std->id,
						'ras_acadYear_relId' => $this->acadYearId,
						'ras_sem_relId' => $this->semId,
						'ras_course_relId' => $c->id,
						'ras_aft_grade_relId' => NULL,
					])
					->all();
				if ($requstedCourses) {
					$contAssMessage .= '<p class="bg-danger"> ' . $c->course_title . ' already requested for re-assessment.</p>';
					continue;
				}
				$lg = \backend\models\GradeScale::findOne(['id' => $course->srd2_grade_relId]);
				$payed = $this->checkPayed($c->id, $std->id);
				$btnn = '';

				$coff = NULL;

				// $inst = $coff->coff2det_inst_relId;
				// $TA = $coff->coff2det_TA_relId;

				$courseNamae = \backend\models\Course::findOne(['id' => $course['srd2_course_relId']]);
				$coff3 = NULL;
				$grrrp = NULL;
				// if the course added, take group of addedWith not the student group setup.
				if (!is_null($course['srd2_coadd_flag'])) { //indicates the course is added.
					$addHistory = \backend\models\AddHistory::findOne(['id' => $course['srd2_coadd_flag']]);
					$addWithGroup = $addHistory->ah_addWithGroup_relId;
					// $evtd = CCF::checkIfEvaluated($addWithGroup,$course->id,$evalueeId,Yii::$app->user->identity->id);
					$coff = CCF::getCourseInstructorForGroup($addWithGroup, $courseNamae->id);
					$grrrp = $addWithGroup;
				} else {
					$coff = CCF::getCourseInstructorForGroup($gset->id, $courseNamae->id);
					$grrrp = $gset->id;
				}
				$instEval = NULL;
				$TAEval = NULL;
				// *******************************************************************************************************

				$evalueeId = is_null($coff->coff2det_inst_relId) ? NULL : \backend\models\Instructor::findOne(['id' => $coff['coff2det_inst_relId']])->inst_user_relId;
				$evalueeId2 = is_null($coff->coff2det_TA_relId) ? NULL : \backend\models\Instructor::findOne(['id' => $coff->coff2det_TA_relId])->inst_user_relId;
				$instEval = is_null($evalueeId) ? TRUE : CCF::checkIfEvaluated($grrrp, $courseNamae->id, $evalueeId, Yii::$app->user->identity->id);
				$TAEval = is_null($evalueeId2) ? TRUE : CCF::checkIfEvaluated($grrrp, $courseNamae->id, $evalueeId2, Yii::$app->user->identity->id);
				// *********************************************************************************************************


				if (($instEval) && ($TAEval)) {
				} else {
					// check evaluation version 2
					if ($course->evaluatedCourseInstructors($grrrp, $courseNamae->id, Yii::$app->user->identity->id)) {
						$instEval = TRUE;
						$TAEval = TRUE;
					} else {
						$contAssMessage .= "<p class='bg-danger'>Semester Evaluation Not completed for the course. >> {$courseNamae->course_title}</p>";
						continue;
					}
				}

				$contAsessMax = 0;
				$bfrFinalMark = 0;


				$catagoryName = \backend\models\CatagoryName2::findOne(['catagory_name' => 'Final Exam']);
				if ($catagoryName) {
					$assesCatagory = \backend\models\ContAssessCatagory2::findOne([
						'cat_course_relId' => $c->id,
						'cat_catagory_relId' => $catagoryName->id,
						'cat_acadYear_relId' => $this->acadYearId,
						'cat_sem_relId' => $this->semId,
					]); //you can get max mark for the catagory here.
					if ($assesCatagory) {
						$contAsessMax = $assesCatagory->cat_maxPoint;
						$markAssignment = \backend\models\InstanceMarkAssignment::find()
							->where([
								'ima_catagorySetup_relId' => $assesCatagory->id
							])
							->one();

						if ($markAssignment) {
							$contAss = \backend\models\ContinuousAssessment2::find()
								->where([
									'ca_semreg_relId' => $course->srd2_semreg_relId,
									'ca_std_relId' => $std->id
								])
								->andWhere(['ca_markAssignment_relId' => $markAssignment->id])
								->one();
							if ($contAss) {
								$bfrFinalMark = $contAss->ca_markObtained;
							} else {
								$contAssMessage .= '<p class="bg-danger">Final exam assessment for <i> ' . $c->course_title . '</i> not made yet. Please wait...</p>';
								continue;
							}
						} else {
							$contAssMessage .= '<p class="bg-danger">Mark Assignemnt not made for this assessment. Please contact course instrcutor of <i>' . $c->course_title . '</i></p>';
							continue;
						}
					} else {
						$contAssMessage .= '<p class="bg-danger">Assessment catagory setup not made for this course in this semester.' . $c->course_title . '</p>';
						continue;
					}
				} else {
					$contAssMessage .= '<p class="bg-danger">Final Exam is not in continous assessment catagory list, Please contact registrar staff if this is error.</p>';
					continue;
				}

				if (is_bool($payed) && $payed) {
					$btnn = "<button class='btn btn-sm btn-success' onclick='req(" . $c->id . "," . $lg->id . "," . $course->id . ",{$bfrFinalMark},{$contAsessMax},{$assesCatagory->id})' title='Request re-assessment for this course.'>Req.</button>";
				} else {
					$btnn = $payed;
				}


				echo "<tr>
		    <td>" . $c->course_title . "</td>
		    <td>" . $lg->gs_ltr_grade . "</td>
		    <td>{$bfrFinalMark}/{$contAsessMax}</td>
		    <td>{$btnn}</td>
		    </tr>";
			}
			echo "</tbody></table>";
		} else {
			echo "<p class='bg-warning'>No course graded in this semester or grades not approved yet.</p>";
		}
		echo $contAssMessage;
		die();
	}

	public function actionGetenrolledcourses2()
	{
		$this->__constructor();
		$std = \backend\models\Student::findOne(['std_user_relId' => Yii::$app->user->identity->id]);

		// check for re-assessment time schedule.
		$gset = $this->currentStudentGroup($std);
		if (!($gset)) {
			die('Group setup not made yet, please come later.');
		}

		$actionName = 'Re-assessment';
		$action = \backend\models\AcademicCalanderAction::findOne(['aca_name' => $actionName]);
		if ($action) {
			$schedule = CCF::getScheduleForAction($gset, $action->id);
			if (!($schedule)) {
				die('Schedule is not open for course re-assessment.');
			}
		} else {
			die('Unregistered action {$actionName}');
		}


		$enrolledCourses = SemesterRegistration2Detail::find()
			->innerJoin('semester_registration_2', 'semester_registration_2_detail.srd2_semreg_relId = semester_registration_2.id')
			->where(['semester_registration_2.semreg_acadYear_relId' => $this->acadYearId])
			->andWhere(['semester_registration_2.semreg_semester_relId' => $this->semId])
			->andWhere(['semester_registration_2.semreg_std_relId' => $std->id])
			->andWhere([' IS NOT ', 'semester_registration_2_detail.srd2_grade_relId', NULL])
			->andWhere(['semester_registration_2_detail.srd2_gradeApproved' => 1])
			->all();

		$contAssMessage = '';
		if ($enrolledCourses) {
			echo "<table class='table table-striped table-condensed table-sm table-bordered'>";
			echo "<caption><p class='bg-success'> <i class='bg-danger'>" . count($enrolledCourses) . "</i> Course(s).</p></caption>";
			echo "<thead>
		    <th>Course Title</th>
		    <th>Grade</th>
		    <th>Final Exam</th>
		    <th>Option</th>
		    </thead><tbody>";
			foreach ($enrolledCourses as $course) {

				$c = \backend\models\Course::findOne(['id' => $course->srd2_course_relId]);
				$requstedCourses = \backend\models\ReassessmentHistory::find()
					->where([
						'ras_std_relId' => $std->id,
						'ras_acadYear_relId' => $this->acadYearId,
						'ras_sem_relId' => $this->semId,
						'ras_course_relId' => $c->id,
						'ras_aft_grade_relId' => NULL,
					])
					->all();
				if ($requstedCourses) {
					$contAssMessage .= '<p class="bg-danger"> ' . $c->course_title . ' already requested for re-assessment.</p>';
					continue;
				}
				$lg = \backend\models\GradeScale::findOne(['id' => $course->srd2_grade_relId]);
				$payed = $this->checkPayed($c->id, $std->id);
				$btnn = '';

				$contAsessMax = 0;
				$bfrFinalMark = 0;


				$catagoryName = \backend\models\CatagoryName2::findOne(['catagory_name' => 'Final Exam']);
				if ($catagoryName) {
					$assesCatagory = \backend\models\ContAssessCatagory2::findOne(['cat_course_relId' => $c->id, 'cat_catagory_relId' => $catagoryName->id]); //you can get max mark for the catagory here.
					if ($assesCatagory) {
						$contAsessMax = $assesCatagory->cat_maxPoint;
						$markAssignment = \backend\models\InstanceMarkAssignment::find()
							->where(['ima_catagorySetup_relId' => $assesCatagory->id])
							->one();
						if ($markAssignment) {
							// $markAssignmentArray = [];

							// foreach ($markAssignment as $key) {

							// }
							$contAss = \backend\models\ContinuousAssessment2::find()
								->where([
									'ca_semreg_relId' => $course->srd2_semreg_relId,
									'ca_std_relId' => $std->id
								])
								->andWhere(['ca_markAssignment_relId' => $markAssignment->id])
								->one();
							if ($contAss) {
								$bfrFinalMark = $contAss->ca_markObtained;
							} else {
								$contAssMessage .= '<p class="bg-danger">Final exam assessment for <i> ' . $c->course_title . '</i> not made yet. Please wait...</p>';
								continue;
							}
						} else {
							$contAssMessage .= '<p class="bg-danger">Mark Assignemnt not made for this assessment. Please contact course instrcutor of <i>' . $c->course_title . '</i></p>';
							continue;
						}
					} else {
						$contAssMessage .= '<p class="bg-danger">Assessment catagory setup not made for this course in this semester.' . $c->course_title . '</p>';
						continue;
					}
				} else {
					$contAssMessage .= '<p class="bg-danger">Final Exam is not under continous assessment catagory list, Please contact regostrar staff if this is error.</p>';
					continue;
				}

				if (is_bool($payed) && $payed) {
					$btnn = "<button class='btn btn-sm btn-success' onclick='req(" . $c->id . "," . $lg->id . "," . $course->id . ",{$bfrFinalMark},{$contAsessMax},{$assesCatagory->id})' title='Request re-assessment for this course.'>Req.</button>";
				} else {
					$btnn = $payed;
				}


				echo "<tr>
		    <td>" . $c->course_title . "</td>
		    <td>" . $lg->gs_ltr_grade . "</td>
		    <td>{$bfrFinalMark}/{$contAsessMax}</td>
		    <td>{$btnn}</td>
		    </tr>";
			}
			echo "</tbody></table>";
		} else {
			echo "<p class='bg-warning'>No course graded in this semester or grades not approved yet.</p>";
		}
		//echo $contAssMessage;
		die();
	}

	private function checkPayed($courseId, $stdId)
	{
		$paymentType = \backend\models\PaymentReason::findOne(['pr_desc' => 'Course Re-assessment']);

		if (!($paymentType)) {
			die('Uknown Payment Type!');
		} else {
			$this->__constructor();

			// ->andWhere(['OR',['ssph_approved'=>1,'ssph_approved'=>NULL]])
			$payentStatus = \backend\models\SelfSponsoredPaymentHistory::find()->where([
				'ssph_acadYear_relId' => $this->acadYearId,
				'ssph_sem_relId' => $this->semId,
				'ssph_pymntRsn_relId' => $paymentType->id,
				'ssph_course_relId' => $courseId,
				'ssph_std_relId' => $stdId,
			])->one();

			if ($payentStatus) {
				if (is_null($payentStatus->ssph_approved)) {
					// Pending...
					return "<p class='bg-warning'>Your Payment request for this course is not approved yet. Please contact the CEP clerk.</p>";
				} else if ($payentStatus->ssph_approved == 1) {
					// Approved
					return TRUE;
				} else {
					// rejected
					return "<p class='bg-warning'>Your Payment request for this course is rejected. Please retry if you want before the schedule for re-assessing closes.</p>";
				}
			} else {
				return "<button class='btn btn-warning btn-xs' title='payment process for requesting re-assessing this course.' onclick='loadPaymentForm({$courseId})'>Process paymet</button>";
			}
		}
	}
}
