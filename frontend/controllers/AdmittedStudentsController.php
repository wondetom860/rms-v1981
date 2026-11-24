<?php

namespace frontend\controllers;
use common\components\CommonControllerFunctions As CCF;
use backend\models\StudentSemesterStatusHistory;
use kartik\mpdf\Pdf;
use yii;


/**
 * This is the model class for table "student".
 *
 * @property integer $id
 * @property integer $std_user_relId
 * @property string $std_regId
 * @property string $std_type
 * @property integer $std_dept_relId
 * @property integer $std_spec_relId
 * @property integer $std_clslvl_relId
 * @property integer $std_prog_relId
 * @property integer $std_admission_relId
 * @property integer $std_acadYear_relId
 * @property integer $std_group_realId
 * @property string $std_semester_status
 * @property string $std_created_at
 * @property integer $std_created_by
 * @property string $std_updated_at
 * @property integer $std_updated_by
 */
class AdmittedStudentsController extends \yii\web\Controller
{


	/*
	
	Draw admitted student report table. 
	Using use defined template.
	WONDE TOM <wondetom@hotmail.com>
	2018/19 Appril 2.

	*/
	public $studentObject = NULL;
	public $acadYearId;
    public $semId;
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    public function actionGetAdmittedStudentListReport(){
        // echo "<h2>Admitted Student List report!</h2>";
        $params = isset($_GET['params']) && !empty($_GET['params'])?(string)$_GET['params']:die("Invalid Request, Selection parameters List empty");
        $params = json_decode($params);

        if (is_object($params)) {
            $acadYearId = isset($params->acadYearId) && !empty($params->acadYearId)?intval($params->acadYearId):die('Select Academic Year');

            $admId = isset($params->admId) && !empty($params->admId)?intval($params->admId):die('Select Admission');
            $progId = isset($params->progId) && !empty($params->progId)?intval($params->progId):die('Select Program');
            $deptId = isset($params->deptId) && !empty($params->deptId)?intval($params->deptId):NULL;
            $specId = isset($params->specId) && !empty($params->specId)?intval($params->specId):NULL;


            $admittedStudents = \backend\models\Student::find()->innerJoin('user','user.id = student.std_user_relId');
            $admittedStudents = $admittedStudents->where([
                    'std_admission_relId'=>$admId,
                    'std_prog_relId'=>$progId,
                    'std_acadYear_relId'=>$acadYearId
                ]);


            if (isset($deptId) && !empty($deptId)) {
                $admittedStudents = $admittedStudents->andWhere(['std_dept_relId'=>$deptId]);
            }

            if (isset($params->nat) && !empty($params->nat)) {
                $admittedStudents = $admittedStudents->andWhere(['user.nationality'=>intval($params->nat)]);
            }

            if (isset($params->specId) && !empty($params->specId)) {
                $admittedStudents = $admittedStudents->andWhere(['std_spec_relId'=>intval($params->specId)]);
            }

            if (isset($params->spon) && !empty($params->spon)) {
                $admittedStudents = $admittedStudents->andWhere(['std_schlrshpType_relId'=>intval($params->spon)]);
            }
            $admittedStudents = $admittedStudents->orderBy([
                'user.first_name' => SORT_ASC,
                'user.middle_name' => SORT_ASC,
                'user.last_name' => SORT_ASC
            ])->all();

            $modelAcadYear = \backend\models\SchoolYear::findOne($acadYearId);
            if ($admittedStudents && count($admittedStudents) > 0) {
                // Yii::$app->response->format = \yii\web\Response::FORMAT_RAW;
                $this->layout = 'printLayout';
                return $this->render('admitted-student-list-report-w8472', [
                    'modelAdm' => \backend\models\Admission::findOne($admId),
                    'modelProg' => \backend\models\Program::findOne($progId),
                    'modelAcadYear' => $modelAcadYear,
                    
                    'modelDept' => isset($params->deptId)?\backend\models\Department::findOne($params->deptId):NULL,
                    'modelSpec' => isset($params->specId)?\backend\models\Specialization::findOne($params->specId):NULL,
                    'modelNat' => isset($params->nat)?\backend\models\Nationality::findOne($params->nat):NULL,
                    'modelSpon' => isset($params->spon)?\backend\models\ScholarshipType::findOne($params->spon):NULL,

                    'admittedStudents'=>$admittedStudents,
                ]);
            }
        }else{
            die("Invalid Query, Selection Parameters list Empity!");
        }
    }
    // get report of admitted students per specification
    public function actionGetAdmittedStudentList(){
        if (Yii::$app->user->can('Commandant') || Yii::$app->user->can('UniversityRegistrar') || Yii::$app->user->can('Admin') ||Yii::$app->user->can('Registrar') || Yii::$app->user->can('AcademicDean')|| \Yii::$app->user->can('clerk')) {
            // safe access by privilages users..
        }else{
            die('<p class="bg-warning">Un-privilaged user.</p>');
        }

        $this->__constructor();

        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId'])?intval($_POST['acadYearId']):die('Select Academic Year');
        // $semId = isset($_POST['semId']) && !empty($_POST['semId'])?intval($_POST['semId']):die('Select Semester');
        $admId = isset($_POST['admId']) && !empty($_POST['admId'])?intval($_POST['admId']):die('Select Admission');
        $progId = isset($_POST['progId']) && !empty($_POST['progId'])?intval($_POST['progId']):die('Select Program');
        // $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])?intval($_POST['clslvlId']):die('Select Class Year.');
        $deptId = isset($_POST['deptId']) && !empty($_POST['deptId'])?intval($_POST['deptId']):NULL;

        $admittedStudents = \backend\models\Student::find()->innerJoin('user','user.id = student.std_user_relId');
            $admittedStudents = $admittedStudents->where([
                    'std_admission_relId'=>$admId,
                    'std_prog_relId'=>$progId,
                    'std_acadYear_relId'=>$acadYearId
                ]);

        if (isset($_POST['spec']) && !empty($_POST['spec'])) {
            $admittedStudents = $admittedStudents->andWhere(['std_spec_relId'=>intval($_POST['spec'])]);
        }

        if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
            $admittedStudents = $admittedStudents->andWhere(['std_dept_relId'=>intval($_POST['deptId'])]);
        }

        if (isset($_POST['nat']) && !empty($_POST['nat'])) {
            $admittedStudents = $admittedStudents->andWhere(['user.nationality'=>intval($_POST['nat'])]);
        }

        if (isset($_POST['spon']) && !empty($_POST['spon'])) {
            $admittedStudents = $admittedStudents->andWhere(['std_schlrshpType_relId'=>intval($_POST['spon'])]);
        }

        $admittedStudents  =$admittedStudents->orderBy([
            'user.first_name' => SORT_ASC,
            'user.middle_name' => SORT_ASC,
            'user.last_name' => SORT_ASC
        ])->all();

         $table = "";
        if ($admittedStudents && count($admittedStudents) > 0) {
            
            $adm = \backend\models\Admission::findOne($admId);
            $prog = \backend\models\Program::findOne($progId);
            $dept = isset($deptId)?\backend\models\Department::findOne($deptId)->dept_name:'All';
            // $yyy = Yii::$app->params['C']['Y'];
            $yyy = \backend\models\SchoolYear::findOne($acadYearId)->sch_year_desc;
            // $clslvl = \backend\models\ClassLevel::findOne($clslvlId);
            $spec = isset($_POST['spec']) && !empty($_POST['spec'])?\backend\models\Specialization::findOne(intval($_POST['spec']))->spec_name:'-';
            $table .= "<table class='table table-bordered table-striped table-condensed table-hover'>";
            $table .= "<caption style='text-align:center;'>
                        <h4><u>Admitted Students List</u></h4>
                        <p>
                        <b>Admission:</b><u>{$adm->admission_name}</u>&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>Program:</b><u>{$prog->program_name}</u><br>
                        <b>Department:</b><u>{$dept}</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>Specialization:</b><u>{$spec}<br>
                        <b>Acdaemic Year:</b><u>{$yyy}
                        </p>
                    </caption>";
            $table .= "<thead style='background-color:#8D0;'>
                    <th>S/N</th>
                    <th>REG. No</th>
                    <th>Full Name</th>
                    <th>Gender</th>
                    <th>Department</th>
                    <th>Nationality</th>
                    <th>Sponsorship Type</th>
                    <th>Option</th>
                    </thead><tbody>";
                    $count = 0;
            foreach ($admittedStudents as $key) {
                // get number of courses enrolled by each student.
                $std = \backend\models\Student::findOne($key->id);
                $user = \backend\models\User::findOne($std->std_user_relId);
                $deptName = $std->std_dept_relId?\backend\models\Department::findOne($std->std_dept_relId)->dept_name:'Not Set.';
                $nation = is_null($user->nationality)||$user->nationality == 0?'Uknown':\backend\models\Nationality::findOne($user->nationality)->name;
                $scholar = \backend\models\ScholarshipType::findOne($std->std_schlrshpType_relId);

                $btn = "<button class='btn ntm-primary btn-xs' onclick='loaadApplicationForm({$user->id});return false;' title='Show Application form'>App. Form</button>";
                // $courseNum = $this->getEnrolledCourse($key->id,$key->semreg_std_relId);
                $table .= "<tr>
                            <td>".++$count."</td>
                            <td>{$std->std_regId}</td>
                            <td>{$user->first_name} {$user->middle_name} {$user->last_name}</td>
                            <td>{$user->gender}</td>
                            <td>{$deptName}</td>
                            <td>{$nation}</td>
                            <td>{$scholar->schType_name}</td>
                            <td>{$btn}</td>
                            <td><span id='err_message_{$key->id}'></span></td>
                        </tr>";
            }
            $table .= "</tbody></table>";

            echo "{$table}";die();

        }else{
            echo "<p class='bg-danger'>No record found.</p>";die();
        }

        // var_dump($admittedStudents);die();
    }



    public function actionIndex(){
        return $this->redirect('report');
    }


    private function getDeptsInYear($clslvlNum){
    	$depts = \backend\models\Department::find()->all();

    	$deptsRow = '';
    	if ($depts && count($depts) > 0) {
    		foreach ($depts as $key) {
    			$deptsRow .= "".$key->dept_name.'<br>' ;
    		}
    		return $deptsRow;
    	}else{
    		return 'No Dept record exists.';
    	}
    }

    private function drawReportTableTemplate($acadYearId){
    	$acadYear = \backend\models\SchoolYear::findOne(['id'=>$acadYearId]);
    	if ($acadYear) {
    		# code...
    	}else{
    		return "<p class='bg-danger'>No such academic year record exists</p>";
    	}
    	$reportTable='';
    	$reportTable .= "<div class='container-fluid table-responsive' style='border:2px solid black;'>";
    	$nationsCount = \backend\models\Nationality::find()->count();
    	$reportTable .= "<table class='table table-condensed table-striped table-sm table-hover table-bordered' style='border:2px solid black;'>
    			<caption style='text-align:center;'>
    				Admitted students report for Academic Year :".$acadYear->sch_year_desc."
    			</caption>
    			<thead style='background-color:#ace;'>
    				<th>Class level</th>
    				<th>Admission</th>
    				<th>Program</th>
    				<th>Department</th>
    				<th>Nationality</th>
    			</thead>
    			<tbody>
    				<tr>
    					<td style='text-align:center; vertical-align:middle;'>1<sup>st</sup> Year</td>
	    				<td rowspan=2>Regular</td>
	    				<td></td>
	    				<td>{$this->getDeptsInYear(1)}</td>
	    				<td></td>
    				</tr>
    				<tr>
    					<td style='text-align:center; vertical-align:middle;'>2<sup>nd</sup> Year</td>
	    				<td rowspan=2></td>
	    				<td></td>
	    				<td></td>
	    				<td></td>
	    				<td></td>
    				</tr>
    				<tr>
    					<td style='text-align:center; vertical-align:middle;'>3<sup>rd</sup> Year</td>
	    				<td rowspan=2></td>
	    				<td></td>
	    				<td></td>
	    				<td></td>
	    				<td></td>
    				</tr>
    				<tr>
    					<td style='text-align:center; vertical-align:middle;'>4<sup>th</sup> Year</td>
	    				<td rowspan=2></td>
	    				<td></td>
	    				<td></td>
	    				<td></td>
	    				<td></td>
    				</tr>
    				<tr>
    					<td style='text-align:center; vertical-align:middle;'>5<sup>th</sup> Year</td>
	    				<td rowspan=2></td>
	    				<td></td>
	    				<td></td>
	    				<td></td>
	    				<td></td>
    				</tr>
    			</tbody>
    			";
    	$reportTable .= "</table></div>";
    	return $reportTable;	
    }

    private function getCountStudent($studentObjectPartial){
    	$fcount = 0;
    	$mcount = 0;

    	$gen = $studentObjectPartial->all();
    	if($gen && count($gen) > 0){
    		foreach ($gen as $key) {
    			if ($key['stdUserRel']['gender'] == 'FEMALE') {
    				$fcount++;
    			}else if ($key['stdUserRel']['gender'] == 'MALE'){
    				$mcount++;
    			}
    		}
    	}

    	return ['FEMALE'=>$fcount,'MALE'=>$mcount];
    }

    private function getGenderColumn($genM){
    	
    	return $this->getCountStudent($genM);
    }

    private function getNationalityColumn($genM){

    	$nationality = \backend\models\Nationality::find()->all();

    	$nlts = [];
    	if ($nationality && count($nationality) > 0) {
    		foreach ($nationality as $key) {
    			$gg = $genM->andFilterWhere(['user.nationality'=>$key->id]);
    			$nlts[$key->name] = $this->getGenderColumn($gg);
    		}
    		return $nlts;
    	}else{
    		return NULL;
    	}

    }

    private function getDepartmentColumn($genM){

    	$departments = \backend\models\Department::find()->all();
    	$ddeps = [];
    	if ($departments && count($departments) > 0) {
    		foreach ($departments as $key) {
    			$dd = $genM->andFilterWhere(['std_dept_relId'=>$key->id]);
    			$ddeps[$key->dept_name] = $this->getNationalityColumn($dd);
    		}
    	return $ddeps;
    	}else{
    		return NULL;
    	}
    }

    private function getProgramColumn($genM){
    	
    	$programs = \backend\models\Program::find()->all();
    	$pprogs = [];
    	if ($programs && count($programs) > 0) {
    		foreach ($programs as $key) {
    			$pp = $genM->andFilterWhere(['std_prog_relId'=>$key->id]);
    			$pprogs[$key->program_name] = $this->getDepartmentColumn($pp);
    		}
    	}
    	return $pprogs;
    	// 'std_prog_relId'=>$programId,
    }

    private function getAdmissionColumn($cslvlId){
    	$genM = $this->studentObject;
    	$genM = $genM->innerJoin('user','user.id = student.std_user_relId')
    		->andFilterWhere([
	    		'std_clslvl_relId'=>$cslvlId,
	    	])
    	->with('stdUserRel');

    	$admissions = \backend\models\Admission::find()->all();

    	$aad = [];
    	if ($admissions && count($admissions) > 0) {
    		foreach ($admissions as $key) {
    			$aa = $genM->andFilterWhere(['std_admission_relId'=>$key->id]);
    			$aad[$key->admission_name] = $this->getProgramColumn($aa);
    		}
    	}
    	return $aad;
    	// 'std_admission_relId'=>$admissionId,
    }

    // get-report-per-year =>GetGeportPerYear
    public function actionGetReportPerYear(){
    	$this->__constructor();
    	$acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId'])?intval($_POST['acadYearId']):die('Invalid Academic Year.');

    	$this->studentObject = \backend\models\Student::find()->filterWhere(['std_acadYear_relId'=>$acadYearId]);
    	//var_dump($this->studentObject->all());die();
    	// getCountStudent(1,1,1,1,1,'FEMALE')
    	$reportTable  = $this->getAdmissionColumn(1);
    	echo "<pre>";
    	print_r($reportTable) ;
    	die();
    }

    // list all valid acadyears.
	public function actionGetAcadYears(){
	    	$yrs = \backend\models\SchoolYear::find()->all();
	    	$options = "<select class=\"form-control\" onchange=\"loadStudentAdmissionReport($(this).val())\">";

	    	$options .= "<option value='' title='Select Academic year for reposrt.'> - </option>";
	    	if ($yrs && count($yrs) > 0) {
	    		foreach ($yrs as $key) {
	    			$options .= "<option value='{$key->id}' title='{$key->id}'> {$key->sch_year_desc} </option>";
	    		}
	    	}

	    	$options .= "</select>";

	    	echo $options;
	    	die();
	    }

    public function actionReport(){
        $model = new StudentSemesterStatusHistory();
    	return $this->render('report',['model'=>$model]);
    }
}
