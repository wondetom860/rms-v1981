<?php

namespace frontend\controllers;

use common\components\CommonControllerFunctions as CCF;
use yii;
use yii\web\Controller;
use \yii\filters\VerbFilter;
use \yii\filters\AccessControl;

class StudentCopyController extends Controller
{

	/**
	 * {@inheritdoc}
	 */
	public function behaviors()
	{
		return [
			'access' => [
				'class' => AccessControl::class,
				'rules' => [
					[
						'actions' => [],
						'allow' => true,
						'roles' => ['@'],
					],
				],
			],
			'verbs' => [
				'class' => VerbFilter::class,
				'actions' => [
					'delete' => ['post'],
				],
			],
		];
	}

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
		$model = new \backend\models\StudentSemesterStatusHistory();
		if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('clerk') || Yii::$app->user->can('Admin')) {

			return $this->render('index', ['model' => $model]);

			// 
		}
		return $this->render('index', ['model' => $model]);
	}

	private function actionStudentCopyReport()
	{
		return $this->render('student_copy_report');
	}

	public function actionReportTable()
	{
		// initialize current acdaYera and Semester

		if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('clerk') || Yii::$app->user->can('Admin')) {
			// return $this->render('student_copy_report');
		} else {
			echo "<p class='bg-danger'>You have no privilage for this action.</p>";
			die();
		}
		$this->__constructor();

		// $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId'])?intval($_POST['acadYearId']):die('acadYearId Error..');
		// $semId = isset($_POST['semId']) && !empty($_POST['semId'])?intval($_POST['semId']):die('semId Error..');
		// $admId = isset($_POST['admId']) && !empty($_POST['admId'])?intval($_POST['admId']):die('Uknown Admission');
		// $progId = isset($_POST['progId']) && !empty($_POST['progId'])?intval($_POST['progId']):die('Uknown Program');
		// $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])?intval($_POST['clslvlId']):die('Uknown Class Year.');
		$gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId']) ? intval($_POST['gsetId']) : (Yii::$app->request->get('gsetId'));
		$studentId = isset($_POST['studentId']) && !empty($_POST['studentId']) ? intval($_POST['studentId']) : (Yii::$app->request->get('studentId'));
		//$requiredStatus = isset($_POST['requiredStatus']) && !empty($_POST['requiredStatus'])?intval($_POST['requiredStatus']):die('Uknown Group.');
		$student = \backend\models\Student::findOne($studentId);
		if ($student) {
			$student->calculateGPAs();
			$indexedSemreg = $student->getSemesterRegistrations()
				->andWhere([' <> ', 'semreg_entry_type', 2])
				->orderBy([
					'semreg_acadYear_relId' => SORT_ASC,//semreg_clslvl_relId
					'semreg_semester_relId' => SORT_ASC
				])
				->all();
		}
		return $this->renderPartial('student_copy_report_3', ['studentModel' => $student, 'semregData' => $indexedSemreg]);
	}

	public function actionReportTableOld()
	{
		// initialize current acdaYera and Semester

		if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('clerk') || Yii::$app->user->can('Admin')) {
			// return $this->render('student_copy_report');
		} else {
			echo "<p class='bg-danger'>You have no privilage for this action.</p>";
			die();
		}
		$this->__constructor();

		// $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId'])?intval($_POST['acadYearId']):die('acadYearId Error..');
		// $semId = isset($_POST['semId']) && !empty($_POST['semId'])?intval($_POST['semId']):die('semId Error..');
		// $admId = isset($_POST['admId']) && !empty($_POST['admId'])?intval($_POST['admId']):die('Uknown Admission');
		// $progId = isset($_POST['progId']) && !empty($_POST['progId'])?intval($_POST['progId']):die('Uknown Program');
		// $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])?intval($_POST['clslvlId']):die('Uknown Class Year.');
		$gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId']) ? intval($_POST['gsetId']) : (Yii::$app->request->get('gsetId'));
		$studentId = isset($_POST['studentId']) && !empty($_POST['studentId']) ? intval($_POST['studentId']) : (Yii::$app->request->get('studentId'));
		//$requiredStatus = isset($_POST['requiredStatus']) && !empty($_POST['requiredStatus'])?intval($_POST['requiredStatus']):die('Uknown Group.');

		$gset = '';

		$gset = \backend\models\WGroupSetup::findOne([
			'id' => $gsetId,
		]);

		if (!($gset)) {
			die('Invalid Group Setup for your selection.');
		}
		$indexedSemreg = [];
		$semreg = \frontend\models\SemesterRegistration2::find()
			->where([
				'semreg_std_relId' => $studentId,
			])
			->all();

		$student = \backend\models\Student::findOne($studentId);
		$user = \common\models\User1::findOne($student->std_user_relId);

		if ($semreg && count($semreg) > 0) {
			foreach ($semreg as $key) {
				$acadYear = \backend\models\SchoolYear::findOne($key->semreg_acadYear_relId)->sch_year_desc;
				$clslvl = \backend\models\ClassLevel::findOne($key->semreg_clslvl_relId)->clslvl_num;
				$semester = \backend\models\Semester::findOne($key->semreg_semester_relId)->semester_name;

				// $semregDetail = \frontend\models\SemesterRegistration2Detail::find()
				// 	->where([
				// 		'srd2_semreg_relId' => $key->id,
				// 	])
				// 	// ->groupBy('srd2_course_relId')
				// 	->all();
				$indexedSemreg[$clslvl][$acadYear][$semester] = [
					$key,
					$key->semesterRegistration2Details,
				];
			}
		} else {
			echo "<p class='bg-danger'>Not Enrolled any course yet.</p>";
			die();
		}

		return $this->renderPartial('student_copy_report_2', ['userModel' => $user, 'studentModel' => $student, 'semregData' => $indexedSemreg]);
		// return $this->redirect('/student-copy/student-copy-report',['userModel'=>$user,'studentModel'=>$student,'semregData'=>$indexedSemreg])->send();



		//  'gas_approved'
		$students2 = \backend\models\WGroupAssignment::find()->where(['gas_gset_relId' => $gset->id])->all();

		// var_dump($students);die();
		$statusTable = '';

		if ($students2 && count($students2) > 0) {
			$statusTable .= "<table id='reportTable' class='table table-bordered' style='font-size:9pt;'>";

			$admission1 = \backend\models\Admission::findOne(['id' => $admId]);
			$program1 = \backend\models\Program::findOne(['id' => $progId]);
			$clslvl1 = \backend\models\ClassLevel::findOne(['id' => $clslvlId]);
			$acadYear1 = \backend\models\SchoolYear::findOne(['id' => $acadYearId]);
			$semester1 = \backend\models\Semester::findOne(['id' => $semId]);

			$statusTable .= "<caption style='text-align:center;'>             
	        </caption>";
			$statusTable .= "<thead style='background-color:#ace;'>
	            <th>RegId</th>
	            <th>Full Name</th>
	            <th>Option</th>
	            </thead><tbody>";
			$count = 0;
			foreach ($students2 as $student) {
				$student = \backend\models\Student::findOne(['id' => $student['gas_std_relId']]);
				$usr = \backend\models\User::findOne(['id' => $student->std_user_relId]);

				$dept = \backend\models\Department::findOne(['id' => $gset->gs_dept_relId]);
				$spec = isset($gset->gs_spec_relId) ? \backend\models\Specialization::findOne(['id' => $gset->gs_spec_relId])->spec_name : '-';


				$statusTable .=  "<tr>
	            <td>" . $student->std_regId . "</td>
	            <td>" . $usr->first_name . ' ' . $usr->middle_name . "</td>
	            <td style='text-align:center;' class='non-print'> 
	                <input class='btn btn-success btn-xs' value='Show' type='button' onClick=\"
	                    showGrade({$gset->id},{$student->id});
	                \" />
	            </td>
	            </tr>";
				$count++;
			}

			$statusTable .= "</tbody></table>";

			echo $statusTable;
		} else {
			die('No student enrolled course in this semester.');
		}
	}


	public function actionReport()
	{
		// initialize current acdaYera and Semester

		if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('clerk') || Yii::$app->user->can('Admin')) {
			// return $this->render('student_copy_report');
		} else {
			echo "<p class='bg-danger'>You have no privilage for this action.</p>";
			die();
		}
		$this->__constructor();

		$acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die('acadYearId Error..');
		$semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('semId Error..');
		$admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Uknown Admission');
		$progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Uknown Program');
		$clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Uknown Class Year.');
		$gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId']) ? intval($_POST['gsetId']) : die('Uknown Group.');
		//$requiredStatus = isset($_POST['requiredStatus']) && !empty($_POST['requiredStatus'])?intval($_POST['requiredStatus']):die('Uknown Group.');

		$gset = '';

		$gset = \backend\models\WGroupSetup::findOne([
			'id' => $gsetId,
			'gs_acadYear_relId' => $acadYearId,
			'gs_sem_relId' => $semId,
			'gs_adm_relId' => $admId,
			'gs_prog_relId' => $progId,
			'gs_clslvl_relId' => $clslvlId,
		]);

		if (!($gset)) {
			die('Invalid Group Setup for your selection.');
		}

		// Loadd all students who registered for the selected semester in that acdaYear.
		$students = \frontend\models\SemesterRegistration2::find()
			->where([
				'semreg_acadYear_relId' => $acadYearId,
				'semreg_semester_relId' => $semId,
			])
			->innerJoin('semester_registration_2_detail', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId');

		$students = $students->andWhere(['semester_registration_2_detail.srd2_group_relId' => $gset->id]);
		$students = $students->with('semesterRegistration2Detail')
			->all();

		//  'gas_approved'
		$students2 = \backend\models\WGroupAssignment::find()->where(['gas_gset_relId' => $gset->id])->all();

		// var_dump($students);die();
		$statusTable = '';

		if ($students2 && count($students2) > 0) {
			$statusTable .= "<table id='reportTable' class='table table-bordered' style='font-size:9pt;'>";

			$admission1 = \backend\models\Admission::findOne(['id' => $admId]);
			$program1 = \backend\models\Program::findOne(['id' => $progId]);
			$clslvl1 = \backend\models\ClassLevel::findOne(['id' => $clslvlId]);
			$acadYear1 = \backend\models\SchoolYear::findOne(['id' => $acadYearId]);
			$semester1 = \backend\models\Semester::findOne(['id' => $semId]);

			$statusTable .= "<caption style='text-align:center;'>             
	        </caption>";
			$statusTable .= "<thead style='background-color:#ace;'>
	            <th>RegId</th>
	            <th>Full Name</th>
	            <th>Gender</th>
	            <th>Nationality</th>
	            <th>Option</th>
	            </thead><tbody>";
			$count = 0;
			foreach ($students2 as $student) {
				$student = $student->gasStdRel;
				$usr = $student->stdUserRel;

				$dept = $gset->gsDeptRel;
				$spec = isset($gset->gs_spec_relId) ? $gset->gsSpecRel->spec_name : '-';
				$nat = $usr->nationality ? $usr->nationalityRel->name : '-';


				$statusTable .=  "<tr>
	            <td>" . $student->std_regId . "</td>
	            <td>" . $usr->getFullName() . "</td>
	            <td>{$usr->gender}</td>
	            <td>{$nat}</td>
	            <td style='text-align:center;' class='non-print'> 
	                <input class='btn btn-success btn-xs' value='Show' type='button' onClick=\"
	                    showGrade({$gset->id},{$student->id});
	                \" />
	            </td>
	            </tr>";
				$count++;
			}

			$statusTable .= "</tbody></table>";

			return $statusTable;
		} else {
			die('No student enrolled course in this semester.');
		}
	}
}
