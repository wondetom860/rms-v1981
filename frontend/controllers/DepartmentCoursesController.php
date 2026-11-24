<?php

namespace frontend\controllers;

use yii;
use yii\helpers\ArrayHelper;
use backend\models\PreRequisitController as PREC;
use backend\models\CourseCatalog;
use yii\filters\VerbFilter;

class DepartmentCoursesController extends \yii\web\Controller
{

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
                       'actions' => ['index'],
                       'allow' => true,
                       'roles' => ['Student',],
                   ],
               ]
           ]
        ];
    }
    public function actionIndex()
    {
        return $this->render('index');
    }

    // returns the cocat history matching the parameters if any for catalog setting.
    public function actionGetCourses(){ 
        $cocats = $this->getCocat();
            // if you decide to display all the courses in the catalog to display, hide the immidiate code below
            $cocats = $cocats->innerJoin('course','course.id = course_catalog.cocat_course_relId')->andWhere(['course.course_dept_relId'=>$_POST['deptId']]);
            
            if (isset($_POST['specId'])) {
                $cocats = $cocats->andWhere([
                    'OR',
                    ['cocat_spec_relId'=>$_POST['specId']],
                    ['cocat_spec_relId'=>NULL]
                ]);
            }
        if (!empty($cocats) && $cocats->count() > 0) {
            $cocats = $cocats->all();
            echo "<table class='table table-condensed table-bordered table-hover'>";
            echo "<caption style='text-align:center;'>
                <p>Department ( Specialization ) course(s).</p>
                </caption>";
            echo "<thead style='background-color:#ace;'>
                <th>#</th>
                <th>Course Code</th>
                <th>Course Title</th>
                <th>Crdt Hr.</th>
                <th>Pre-requisit Course(s)</th>
                </thead>";
            $count = 0;
            foreach ($cocats as $cocat) {
                $cprec = "";
                $c = \backend\models\Course::findOne(['id'=>$cocat['cocat_course_relId']]);
                $prec = PREC::hasPrerequisit2($cocat);
                if ($prec && count($prec) > 0) {
                    foreach ($prec as $keyPrec) {
                        $cprec .= "{$keyPrec->preqPreqCourseRel->course_title}-<b>[{$keyPrec->preqPreqCourseRel->getCourseCredits()}]</b><br>";
                    }
                }else{
                    $cprec = "Nill";
                }
                // $cprec =is_null($prec)?'None':\backend\models\Course::findOne(['id'=>$prec])->course_code;
                echo "<tr>
                	<td>".++$count."</td>
                	<td>". $c->course_code ."</td>
                	<td>".$c->course_title ."</td>
                	<td>{$c->getCourseCredits()}</td>
                	<td>{$cprec}</td>
                	</tr>";
            }
            echo "</table>";
        }else{
            echo "<span style='color:#ace; font-style:italic;'>No History found for your selection.</span>";
	        
	    }
    }

    public function actionGetSelectionForm(){
    	//$userRole = Yii::$app->user->identity->id;
    	$userRole = array_keys(Yii::$app->authManager->getRolesByUser(Yii::$app->user->identity->id))[0];

    	$admission = null;
    	$program = null;
    	$dept = null;
    	$clslvl = null;
    	$spec = null;
    	$semester = null;
        $acadYear = NULL;

    	$container = "<div class='container-fluid'><div class='row'>";

    	$admSelect = "<div class='col-md-2'><p>Admission</p>
            <select class='form-control' onchange='loaadCourses()' id='admiddd'>
            <option value=''> - </option>";
    	$progSelect = "<div class='col-md-2'><p>Program</p>
            <select class='form-control' onchange='loadClslvl($(this).val());loaadCourses();' id='progIdd'>
            <option value=''> - </option>";
    	$deptSelect = "<div class='col-md-2'><p>Department</p>
            <select class='form-control'onchange='loadSpec($(this).val(),$(\"#progIdd\").val());loaadCourses();' id='deptidddd'>
            <option value=''> - </option>";
    	$clslvlSelect = "<div class='col-md-1'><p>Year</p>
            <select class='form-control' onchange='loaadCourses()' id='clslvlId'>
            <option value=''> - </option>";
    	$specSelect = "<div class='col-md-3'><p>Specialization</p>
            <select class='form-control' onchange='loaadCourses()' id='specdddd'>
            <option value=''> - </option>";
    	$semSelect = "<div class='col-md-2'><p>Semester</p>
            <select class='form-control' onchange='loaadCourses()' id='semddd'>
            <option value=''> - </option>";

    	if ($userRole == 'Instructor' || $userRole == 'DepartmentHead') {
    		$deptId = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id])->inst_dep_relId;
    		$dept = \backend\models\Department::find()->where(['id'=>$deptId])->all();
    	}else if( $userRole == 'Student'){
    		$std = \backend\models\Student::findOne(['std_user_relId'=>Yii::$app->user->identity->id]);
    		$dept = \backend\models\Department::find()->where(['id'=>$std->std_dept_relId])->all();
    		$admission = \backend\models\Admission::find()->where(['id'=>$std->std_admission_relId])->all();
    		$program = \backend\models\Program::find()->where(['id'=>$std->std_prog_relId])->all();
    	}



		$admission = is_null($admission)?\backend\models\Admission::find()->all():$admission;
		$program = is_null($program)?\backend\models\Program::find()->all():$program;
		$dept = is_null($dept)?\backend\models\Department::find()->all():$dept;
		$clslvl = is_null($clslvl)?\backend\models\ClassLevel::find()->all():$clslvl;
		$spec = is_null($spec)?\backend\models\Specialization::find()->all():$spec;
		$semester = \backend\models\Semester::find()->all();

		foreach ($admission as $key) {
			$admSelect .= "<option value='{$key->id}'>".$key->admission_name."</option>";
		}
		$admSelect .= "</select></div>";
		$container .= $admSelect;

		foreach ($program as $key) {
			$progSelect .= "<option value='{$key->id}'>".$key->program_name."</option>";
		}
		$progSelect .= "</select></div>";
		$container .=$progSelect;

		foreach ($dept as $key) {
			$deptSelect .= "<option value='{$key->id}'>".$key->dept_name."</option>";
		}
		$deptSelect .= "</select></div>";
		$container .=$deptSelect;

		foreach ($clslvl as $key) {
			$clslvlSelect .= "<option value='{$key->id}'>".$key->clslvl_name."</option>";
		}
		$clslvlSelect .= "</select></div>";
		$container .=$clslvlSelect;

		foreach ($spec as $key) {
			$specSelect .= "<option value='{$key->id}'>".$key->spec_name."</option>";
		}
		$specSelect .= "</select></div>";
		$container .=$specSelect;

		foreach ($semester as $key) {
			$semSelect .= "<option value='{$key->id}'>".$key->semester_name."</option>";
		}
		$semSelect .= "</select></div>";
		$container .= $semSelect;

		$container .= "</div><hr>
		<div class='row' style='text-align:center;' onclick='loaadCourses()'><button class='btn btn-success btn-sm'>Load Courses</button></div>
		</div>";

		echo $container;die();

    }

    protected function objectCount($model){
    	return count($model) <= 1;
    }

    // common get file for cocat list gfiltering
    protected function getCocat(){
        $cocats = CourseCatalog::find();

        if (isset($_POST['admId']) && !empty($_POST['admId'])) {
            $cocats = $cocats->Where([ 'cocat_admission_relId'=>$_POST['admId'],]);
        }else{
            die('Select Admission');
        }

        if (isset($_POST['progId']) && !empty($_POST['progId'])) {
            $cocats = $cocats->andWhere([ 'cocat_program_relId'=>$_POST['progId'],]);
        }else{
            die('Select Program');
        }

        if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
            $cocats = $cocats->andWhere([ 'OR',['cocat_dept_relId'=>$_POST['deptId']],['cocat_dept_relId'=>NULL ]]);
        }else{
            die('Select Department');
        }

        if (isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])) {
            $cocats = $cocats->andWhere([ 'OR',['cocat_clslvl_relId'=>$_POST['clslvlId']],['cocat_clslvl_relId'=>NULL]]);
        }

        if (isset($_POST['specId']) && !empty($_POST['specId'])) {
            $cocats = $cocats->andWhere([ 'OR',['cocat_spec_relId'=>$_POST['specId']],['cocat_spec_relId'=>null]
            ]);
        }
        if (isset($_POST['semId']) && !empty($_POST['semId'])) {
            // $cocats = $cocats->andFilterWhere([ 'cocat_sem_relId'=>$_POST['semId'],]);
            $cocats = $cocats->andWhere([ 'OR',['cocat_sem_relId'=>$_POST['semId']],['cocat_sem_relId'=>NULL]]);
        }

        return $cocats;
    }
}
