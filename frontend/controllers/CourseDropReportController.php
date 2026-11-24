<?php

namespace frontend\controllers;

class CourseDropReportController extends \yii\web\Controller
{
    public $acadYearId = null;
    public $semId = null;
    protected function __constructor(){

        $this->acadYearId = \backend\models\SchoolYear::findOne(['sch_year_desc'=>Yii::$app->params['C']['Y']])->id;
        $this->semId = \backend\models\Semester::findOne(['semester_name'=>Yii::$app->params['C']['S']])->id;
    }
    public function actionIndex()
    {

        return $this->render('index');
    }

    public function actionGetReportTable(){

    	$acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId'])?intval($_POST['acadYearId']):die('Select Academic Year');
    	$semId = isset($_POST['semId']) && !empty($_POST['semId'])?intval($_POST['semId']):die('Select Semester');

    	$requests = \backend\models\CourseDropHistory::find()->where([
    		'cdh_acadYear_relId'=>$acadYearId,
    		'cdh_sem_relId'=>$semId,
    	])->orderBy('cdh_status')->all();

    	$tab = '';

    	if ($requests && count($requests) > 0) {
    		$tab .= "<table class='table table-striped table-condensed table-sm table-bordered'>";
		    //echo "<caption><p class='bg-success'> <i class='bg-danger'>". count($stds) ."</i> Students.</p></caption>";
		    $tab .= "<thead style='background-color:#ace;'>
			    <th>#</th>
			    <th>RegId</th>
			    <th>Full Name</th>
			    <th>Department</th>
			    <th>Requested Course</th>
			    <th>Group</th>
			    <th>Status</th>
		    </thead><tbody>";
		    $count = 0;
			foreach ($requests as $key) {
				$gset = $key->cdhStdRel->stdGroupReal;

				$Status = is_null($key->cdh_status)?'Pending':($key->cdh_status == 1?'Approved':'Rejected');

				$dept = $gset->gsDeptRel->dept_name;
			    $student = \backend\models\Student::findOne(['id'=>$key->cdh_std_relId]);
			    $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
			    
			    $spec = is_null($gset->gs_spec_relId)?$dept:$dept.' ['.\backend\models\Specialization::findOne(['id'=>$gset->gs_spec_relId])->spec_name.' ]';
			    $tab .= "<tr>
			    <td>". ++$count ."</td>
			    <td>". $student->std_regId ."</td>
			    <td>". $usr->getFullName() ."</td>
			    <td>". $dept ."</td>
			    <td>{$key->cdhCourseRel->course_title}</td>
			    <td title={$spec}>{$gset->gsGroupRel->group_name}</td>
			   
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
    		echo "<p class='bg-danger'>No course Drop request made in this semester.</p>";die();
    	}
    }

    public function actionGetAcads(){
    	$acadyearsSelect = '<option value=""> - </option>';
    	$semSelect = '<option value=""> - </option>';
    	$admSelect = '<option value=""> - </option>';
    	$progSelect = '<option value=""> - </option>';
    	$acadyears = \backend\models\SchoolYear::find()->all();

    	if ($acadyears && count($acadyears) > 0) {
    		foreach ($acadyears as $key) {
    			$acadyearsSelect .= "<option value='{$key->id}'>{$key->sch_year_desc}</option>";
    		}
    	}

    	$sems = \backend\models\Semester::find()->all();

    	if ($sems && count($sems) > 0) {
    		foreach ($sems as $key) {
    			$semSelect .= "<option value='{$key->id}'>{$key->semester_name}</option>";
    		}
    	}

    	$adms = \backend\models\Admission::find()->all();

    	if ($adms && count($adms) > 0) {
    		foreach ($adms as $key) {
    			$admSelect .= "<option value='{$key->id}'>{$key->admission_name}</option>";
    		}
    	}

    	$progs = \backend\models\Program::find()->all();

    	if ($progs && count($progs) > 0) {
    		foreach ($progs as $key) {
    			$progSelect .= "<option value='{$key->id}'>{$key->program_name}</option>";
    		}
    	}

    	echo json_encode(['acadYear'=>$acadyearsSelect,'sem'=>$semSelect,'adm'=>$admSelect,'prog'=>$progSelect]);die();
    }

}

