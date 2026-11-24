<?php

namespace frontend\controllers;
use common\components\CommonControllerFunctions As CCF;

class SemesterAdvisorController extends \yii\web\Controller
{

	public $acadYearId;
    public $semId;
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }


    public function actionIndex()
    {
    	if (\Yii::$app->user->can('DepartmentHead') || \Yii::$app->user->can('Registrar')|| \Yii::$app->user->can('clerk')|| \Yii::$app->user->can('Admin')|| \Yii::$app->user->can('Commandant')|| \Yii::$app->user->can('PGCoordinator')|| \Yii::$app->user->can('AcademicDean')) {
    		$model = new \backend\models\StudentSemesterStatusHistory();
        	return $this->render('index',['model'=>$model]);
    	}
    	return $this->render('index_report',['model'=>[]]);
    }

    public function actionCreate(){
    	if (\Yii::$app->user->can('DepartmentHead')) {
    		// die(intval($_POST['admId']));
    		$admId = isset($_POST['admId']) && !empty($_POST['admId'])?intval($_POST['admId']):die('Uknown Admission');
		    $progId = isset($_POST['progId']) && !empty($_POST['progId'])?intval($_POST['progId']):die('Uknown Program');
		    $deptId = isset($_POST['deptId']) && !empty($_POST['deptId'])?intval($_POST['deptId']):die('Uknown Department');
		    $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])?intval($_POST['clslvlId']):die('Uknown Class Level');
		    $instId = isset($_POST['instId']) && !empty($_POST['instId'])?intval($_POST['instId']):die('Uknown Instrutor. ');

		    $this->__constructor();

		    $model = new \backend\models\SemesterAdvisor();

		    $model->sa_acadYear_relId = $this->acadYearId;
		    $model->sa_sem_relId = $this->semId;
		    $model->sa_adm_relId = $admId;
		    $model->sa_prog_relId = $progId;
		    $model->sa_dept_relId = $deptId;
		    $model->sa_clslvl_relId = $clslvlId;
		    $model->sa_inst_relId = $instId;

		    $model->sa_created_at = date('Y-m-d h:i:s');
		    $model->sa_created_by = \Yii::$app->user->identity->id;

		    if ($model->save()) {
		    	echo 1;
		    }else{
		    	var_dump($model->getErrors());
		    }
		    die();
    	}else{
    		echo "<p class='bg-danger'>Un-privilleged user.</p>";
    	}
    	die();
    }

    public function actionForm(){
    	$this->__constructor();
    	$model  = new \backend\models\SemesterAdvisor();
    	$instdept = NULL;
    	if (\Yii::$app->user->can('DepartmentHead')) {
    		$instdept = \backend\models\Instructor::findOne(['inst_user_relId'=>\Yii::$app->user->identity->id])->inst_dep_relId;

    	}
    	$gsets = \backend\models\WGroupSetup::find()
    		->where([
    			'gs_acadYear_relId'=>$this->acadYearId,
    			'gs_sem_relId'=>$this->semId,
    			'gs_dept_relId'=>$instdept,
    		])
    		->orderBy('gs_clslvl_relId')
    		->groupBy('gs_clslvl_relId,gs_adm_relId,gs_prog_relId')
    		->all();

    	if ($gsets && count($gsets) > 0) {

    		$rep = "";

    		foreach ($gsets as $key) {
    			$exists = $model->getSemesterAdvisor($key);

    			if ($exists && count($exists) > 0) {
    				continue;
    			}else{
    				$admission = \backend\models\Admission::findOne($key->gs_adm_relId);
		    		$program = \backend\models\Program::findOne($key->gs_prog_relId);
		    		$dept = \backend\models\Department::findOne($key->gs_dept_relId);
		    		$clslvl = \backend\models\ClassLevel::findOne($key->gs_clslvl_relId);

		    		$options = "<option value=''> - </option>";
		    		$insts = \backend\models\Instructor::find()->where(['inst_dep_relId'=>$dept->id])->all();

		    		if ($insts && count( $insts) > 0) {
		    			foreach ($insts as $kew) {
		    				$user = \common\models\User1::findOne(['id'=>$kew->inst_user_relId,'activated'=>1]);
		    				if ($user) {
		    					$options .= "<option value={$kew->id}> {$user->first_name} {$user->middle_name}</option>";
		    				}		    				
		    			}
		    		}
		    		//  = \yii\helpers\ArrayHelper::map(,'id', 'instUserRel.first_name');
		    		$istInput = "<select onchange='assignAdvisor({$admission->id},{$program->id},{$dept->id},{$clslvl->id},$(this).val())'>{$options}</select> ";
    				$rep .= "
    					<div class='container-fluid' style='margin:10px;'>
    						<div class='row form-control'>
	    						<div class='col-md-2 form-group'>{$admission->admission_name}</div>
	    						<div class='col-md-2 form-group'>{$program->program_name}</div>
	    						<div class='col-md-3 form-group'>{$dept->dept_name}</div>
	    						<div class='col-md-2 form-group'>Year - {$clslvl->clslvl_num}</div>
	    						<div class='col-md-3 form-group'>{$istInput}</div>
	    					</div>
    					</div>
    				";
    			}
    		}

    		return $rep;
    	}else{
    		echo "<p class='bg-warning'>No Active Student Found.</p>";
    	}
    }

    public function actionDelete(){
    	return \backend\models\SemesterAdvisor::findOne(intval($_POST['id']))->delete();
    }
    public function actionReport(){
    	// admId:admId,
	    // progId:progId,
	    // clslvlId:clslvlId,
	    // deptId:deptId,
	    // acadYearId:acadYearId,
	    // semId:semId

    	$this->__constructor();
	    $acadYear = isset($_POST['acadYearId']) && !empty($_POST['acadYearId'])?intval($_POST['acadYearId']):$this->acadYearId;
	    $sem = isset($_POST['semId']) && !empty($_POST['semId'])?intval($_POST['semId']):$this->semId;

	    $admId = isset($_POST['admId']) && !empty($_POST['admId'])?intval($_POST['admId']):NULL;
	    $progId = isset($_POST['progId']) && !empty($_POST['progId'])?intval($_POST['progId']):NULL;
	    $deptId = isset($_POST['deptId']) && !empty($_POST['deptId'])?intval($_POST['deptId']):NULL;
	    $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])?intval($_POST['clslvlId']):NULL;

	    if (\Yii::$app->user->can('DepartmentHead')) {
	    	$deptId = \backend\models\Instructor::findOne(['inst_user_relId'=>\Yii::$app->user->identity->id])->inst_dep_relId;
	    }

	    // get group setups per selection::

	    $advisors = \backend\models\SemesterAdvisor::find()
	    	->innerJoin('class_level','class_level.id = semester_advisor.sa_clslvl_relId')
	    	->where(['sa_acadYear_relId'=>$acadYear,'sa_sem_relId'=>$sem,]);

	    if (!is_null($admId)) {$advisors = $advisors->andWhere(['sa_adm_relId'=>$admId]);}
	    if (!is_null($progId)) {$advisors = $advisors->andWhere(['sa_prog_relId'=>$progId]);}
	    if (!is_null($deptId)) {$advisors = $advisors->andWhere(['sa_dept_relId'=>$deptId]);}
	    if (!is_null($clslvlId)) {$advisors = $advisors->andWhere(['sa_clslvl_relId'=>$clslvlId]);}

	    $advisors = $advisors->orderBy('sa_dept_relId,class_level.clslvl_num')->all();


	    if ($advisors && count($advisors) > 0) {
	    	echo "<div class='row'>";
	    	foreach ($advisors as $key) {
	    		$saInst = \backend\models\Instructor::findOne($key->sa_inst_relId);
	    		$user = \common\models\User1::findOne($saInst->inst_user_relId);

	    		$acr = 'B.Sc.';

	    		$acr = $saInst->instAcrRel->acr_name;

	    		$admission = \backend\models\Admission::findOne($key->sa_adm_relId);
	    		$program = \backend\models\Program::findOne($key->sa_prog_relId);
	    		$dept = \backend\models\Department::findOne($key->sa_dept_relId);
	    		$clslvl = \backend\models\ClassLevel::findOne($key->sa_clslvl_relId);

	    		$year = \backend\models\SchoolYear::findOne($key->sa_acadYear_relId);
	    		$sem = \backend\models\Semester::findOne($key->sa_sem_relId);

	    		echo "<div class='col-md-2'>
    					<div class='panel panel-default'>
    						<div class='panel-heading'>Dep : {$dept->dept_code}
    						<br> Year : {$clslvl->clslvl_num}
    						<br>Program : <i><b>[{$admission->admission_name[0]} {$program->program_name[0]}] </b></i>
    						<br> <i><b> Semester {$sem->semester_name}</b></i> of <i><b>{$year->sch_year_desc}</b></i>
    						</div>
    						<div class='panel-body'>
    							<center>
    								<img src='/uploads/".$user->photo_num."' width='100' height='140' class='img-circle' />
    								<p style='font-weight:bold;'>{$user->first_name} {$user->middle_name} <i>({$acr})</i></p>
    							</center>
    							";
    							if (\Yii::$app->user->can('DepartmentHead')) {
    								echo "<hr><button class='btn btn-warning btn-xs pull-right' title='Change Advisor.' onclick='changeSA({$key->id})'>Change</button>";
    							}
    			echo "
    						</div>
    					</div>
    				</div>";

	    	}
	    	echo "</div>";
	    }else{
	    	echo "<p class='bg-danger'>No advisor assigned.</p>";
	    }
	    if (\Yii::$app->user->can('DepartmentHead')) {
		    	echo "<p><button class='btn btn-primary btn-sm' onclick='loadForm();return false;'>Assign Advisor</button></p>";
		    }
	   die();
    }

}
