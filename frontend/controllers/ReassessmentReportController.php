<?php

namespace frontend\controllers;
use yii;

class ReassessmentReportController extends \yii\web\Controller
{

/**
 * This is the model class for table "reassessment_history".
 *
 * @property integer $id
 * @property integer $ras_std_relId
 * @property integer $ras_srd2_relId
 * @property integer $ras_course_relId
 * @property integer $ras_acadYear_relId
 * @property integer $ras_sem_relId
 * @property integer $ras_group_relId
 * @property integer $ras_clslvl_relId
 * @property integer $ras_status
 * @property integer $ras_approved_by
 * @property integer $ras_bef_grade_relId
 * @property string $ras_app_date
 * @property string $ras_created_at
 * @property string $ras_updated_at
 * @property integer $ras_updated_by
 * @property integer $ras_aft_grade_relId
 *
 * @property User $rasApprovedBy
 * @property Student $rasStdRel
 * @property Course $rasCourseRel
 * @property SchoolYear $rasAcadYearRel
 * @property Semester $rasSemRel
 * @property WGroupSetup $rasGroupRel
 * @property ClassLevel $rasClslvlRel
 * @property GradeScale $rasBefGradeRel
 * @property GradeScale $rasAftGradeRel
 */
    public function actionIndex()
    {
        return $this->render('index');
    }

    public function actionGetReportTable(){
    	$acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId'])?intval($_POST['acadYearId']):die('Select Academic Year');
    	$semId = isset($_POST['semId']) && !empty($_POST['semId'])?intval($_POST['semId']):die('Select Semester');

    	if (Yii::$app->user->can('Student')) {

    		$std = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);

    		$requests = \backend\models\ReassessmentHistory::find()->where([
	    		'ras_acadYear_relId'=>$acadYearId,
	    		'ras_sem_relId'=>$semId,
	    		'ras_std_relId'=>$std->id,
	    	])->all();

    	}else{
    		$requests = \backend\models\ReassessmentHistory::find()->where([
    		'ras_acadYear_relId'=>$acadYearId,
    		'ras_sem_relId'=>$semId,
    	])->all();

    	}

    	$tab = '';

    	if ($requests) {
    		$tab .= "<table class='table table-striped table-condensed table-sm table-bordered'>";
		    //echo "<caption><p class='bg-success'> <i class='bg-danger'>". count($stds) ."</i> Students.</p></caption>";
		    $tab .= "<thead style='background-color:#ace;'>
			    <th>#</th>
			    <th>RegId</th>
			    <th>Full Name</th>
			    <th>Department</th>
			    <th>Requested Course</th>
			    <th>Group</th>
			    <th>B. Grd</th>
			    <th>A. Grd</th>
			    <th>Status</th>
		    </thead><tbody>";
		    $count = 0;
			foreach ($requests as $key) {
				$gset = \backend\models\WGroupSetup::findOne(['id'=>$key->ras_group_relId]);

				// Leave those not in the selected admission.
				if (isset($_POST['adm']) && !empty($_POST['adm']) && $gset->gs_adm_relId != $_POST['adm']) {
					continue;
				}

				// Leave those not in selected program
				if (isset($_POST['prog']) && !empty($_POST['prog']) && $gset->gs_prog_relId != $_POST['prog']) {
					continue;
				}

				$Status = is_null($key->ras_status)?'Pending':($key->ras_status == 1?'Approved':'Rejected');
				// if (isset($_POST['prog']) && !empty($_POST['prog']) && $gset->gset_prog_relId != $_POST['prog']) {
				// 	continue;
				// }

				$dept = \backend\models\Department::findOne(['id'=>$gset->gs_dept_relId])->dept_name;
			    $student = \backend\models\Student::findOne(['id'=>$key->ras_std_relId]);
			    $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
			    $bg = isset($key->ras_bef_grade_relId)?\backend\models\GradeScale::findOne(['id'=>$key->ras_bef_grade_relId])->gs_ltr_grade:'-';
			    $ag = isset($key->ras_aft_grade_relId)?\backend\models\GradeScale::findOne(['id'=>$key->ras_aft_grade_relId])->gs_ltr_grade:'-';

			    $clslvl = \backend\models\ClassLevel::findOne(['id'=>$gset->gs_clslvl_relId])->clslvl_num;
			    $spec = is_null($gset->gs_spec_relId)?($dept.' Year '.$clslvl) : $dept.' Year '.$clslvl.' ['.\backend\models\Specialization::findOne(['id'=>$gset->gs_spec_relId])->spec_name.' ]';

			    $tab .= "<tr>
			    <td>". ++$count ."</td>
			    <td>". $student->std_regId ."</td>
			    <td>". $usr->first_name.' '. $usr->middle_name ."</td>
			    <td>". $dept ."</td>
			    <td>". \backend\models\Course::findOne(['id'=>$key->ras_course_relId])->course_title ."</td>
			    <td title={$spec}>". \backend\models\WGroup::findOne(['id'=>$gset->gs_group_relId])->group_name ."</td>
			    <td>". $bg ."</td>
			    <td>". $ag ."</td>
			    <td>". $Status ."</td>
			    </tr>";
			}
		$tab .= "</tbody>
				<tfoot>
					<td colspan =2>Total Requests: {$count}</td>
				</tfoot>
			</table>";
		echo $tab; die();
    	}else{
    		echo "<p class='bg-danger'>No course re-assessment request made in this semester.</p>";die();
    	}
    }

    public function actionGetAcads(){
    	$acadyearsSelect = '<option value=""> - </option>';
    	$semSelect = '<option value=""> - </option>';
    	$admSelect = '<option value=""> - </option>';
    	$progSelect = '<option value=""> - </option>';
    	$acadyears = \backend\models\SchoolYear::find()->all();

    	if ($acadyears) {
    		foreach ($acadyears as $key) {
    			$acadyearsSelect .= "<option value='{$key->id}'>{$key->sch_year_desc}</option>";
    		}
    	}

    	$sems = \backend\models\Semester::find()->all();

    	if ($sems) {
    		foreach ($sems as $key) {
    			$semSelect .= "<option value='{$key->id}'>{$key->semester_name}</option>";
    		}
    	}

    	$adms = \backend\models\Admission::find()->all();

    	if ($adms) {
    		foreach ($adms as $key) {
    			$admSelect .= "<option value='{$key->id}'>{$key->admission_name}</option>";
    		}
    	}

    	$progs = \backend\models\Program::find()->all();

    	if ($progs) {
    		foreach ($progs as $key) {
    			$progSelect .= "<option value='{$key->id}'>{$key->program_name}</option>";
    		}
    	}

    	echo json_encode(['acadYear'=>$acadyearsSelect,'sem'=>$semSelect,'adm'=>$admSelect,'prog'=>$progSelect]);die();
    }

}
