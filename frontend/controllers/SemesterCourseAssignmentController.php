<?php

namespace frontend\controllers;
use common\components\CommonControllerFunctions as CCF;
use backend\models\WGroupSetup;
use backend\models\User;
use backend\models\Instructor;
use yii\helpers\ArrayHelper;
use yii;

class SemesterCourseAssignmentController extends \yii\web\Controller
{

	public $acadYearId;
    public $semId;
    public $coff2;
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

    public function actionIndex()
    {
    	$coff = new \backend\models\StudentSemesterStatusHistory();
        return $this->render('index',['model'=>$coff]);
    }

    public function actionReport(){
    	 $this->__constructor();

	    $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId'])?intval($_POST['acadYearId']):$this->acadYearId;
	    $semId = isset($_POST['semId']) && !empty($_POST['semId'])?intval($_POST['semId']):$this->semId;
	    $admId = isset($_POST['admId']) && !empty($_POST['admId'])?intval($_POST['admId']):die('Uknown Admission');
	    $progId = isset($_POST['progId']) && !empty($_POST['progId'])?intval($_POST['progId']):die('Uknown Program');
	    $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])?intval($_POST['clslvlId']):NULL;
	    $gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId'])?intval($_POST['gsetId']):NULL;


	    // echo "".$gsetId;die();

	   // Group setup
	    $gset = NULL;
	    // $gset = \backend\models\WGroupSetup::find()->where(['gs_acadYear_relId'=>$acadYearId,'gs_sem_relId'=>$semId, 'gs_admiddion_relId'=>])

	    if (is_null($gsetId)) {
	    	$groups = WGroupSetup::find()->where([
            'gs_acadYear_relId'=>$acadYearId,
            'gs_sem_relId'=>$semId,
            'gs_adm_relId'=>$admId,
            'gs_prog_relId'=>$progId,
            ]);
	        if (isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])) {
	            $groups->andFilterWhere(['gs_clslvl_relId'=>$clslvlId,]);
	        }
	        if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
	            $groups->andFilterWhere(['gs_spec_relId'=>$_POST['deptId']]);
	        }
	        if (isset($_POST['specId']) && !empty($_POST['specId'])) {
	            $groups->andFilterWhere(['gs_spec_relId'=>$_POST['specId']]);
	        }
	        $gset = $groups->all();
	    }else{
	    	$gset = WGroupSetup::find()->where(['id'=>$gsetId])->all();
	    }


	    $gsetIdArray = [];
	    if ($gset && count($gset) > 0) {
	    	foreach ($gset as $key) {
	    		$gsetIdArray[] = $key['id'];
	    	}
	    }else{
	    	echo "ERROR: You are not in correct time for this repert.";
	    }
	    // course offering
        // ->andWhere([' IS NOT ','coff2det_inst_relId',NULL])

	    $coff3 = \backend\models\CourseOffering2Detail::find()
	    ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
	    ->where(['coff2_acadYear_relId'=>$acadYearId,'coff2_sem_relId'=>$semId])
	    ->andWhere(['coff2_group_relId'=>$gsetIdArray])
	    
	    ->with('coff2detCoff2Rel')
	    ->orderBy('course_offering2.coff2_group_relId ASC')
	    ->distinct('id')
	    ->all();

	    if ($coff3 && count($coff3) > 0) {
	    	$this->GetInstructorCourseAssignmentHistory($coff3,$gset[0]);
	    }else{
	    	echo "<p class='bg-danger'>Course not offered yet.</p>";die();
	    }
    }


    protected function getInstName($insts){
    	$instName = [];
    		foreach ($insts as $inst) {
    			$id = $inst['inst_user_relId'];
    			//$istId = \backend\models\Instructor::findOne(['id'=>$id])->inst_user_relId;
				$uid = \backend\models\User::findOne(['id'=>$id]);
				array_push($instName,$uid->first_name.' '.$uid->middle_name);
	    		}
	    		return $instName;
			}

    protected function getCoffInstName($coffInstrRelId){
        // returns the instructor full name from course_offering detail.

        if (is_null($coffInstrRelId)) {
            return 'N/A';
        }else{
            $usrId = User::findOne(['id'=>Instructor::findOne(['id'=>$coffInstrRelId])->inst_user_relId]);
            return $usrId->first_name .' '.$usrId->middle_name;
        }
    }

    public function actionReportPrint(){
        $params = isset($_GET['params']) && !empty($_GET['params'])?(string)$_GET['params']:die('Missing Information for generating report.');
        $params = json_decode($params);
        if (is_object($params)) {
            $this->__constructor();

            $acadYearId = isset($params->acadYearId) && !empty($params->acadYearId)?intval($params->acadYearId):$this->acadYearId;
            $semId = isset($params->semId) && !empty($params->semId)?intval($params->semId):$this->semId;
            $admId = isset($params->admId) && !empty($params->admId)?intval($params->admId):die('Uknown Admission');
            $progId = isset($params->progId) && !empty($params->progId)?intval($params->progId):die('Uknown Program');
            $clslvlId = isset($params->clslvlId) && !empty($params->clslvlId)?intval($params->clslvlId):NULL;
            $gsetId = isset($params->gsetId) && !empty($params->gsetId)?intval($params->gsetId):NULL;

            // $this->layout = 'printLayout';
            // echo "".$gsetId;die();

           // Group setup
            $gset = NULL;
            // $gset = \backend\models\WGroupSetup::find()->where(['gs_acadYear_relId'=>$acadYearId,'gs_sem_relId'=>$semId, 'gs_admiddion_relId'=>])

            if (is_null($gsetId)) {
                $groups = WGroupSetup::find()->where([
                'gs_acadYear_relId'=>$acadYearId,
                'gs_sem_relId'=>$semId,
                'gs_adm_relId'=>$admId,
                'gs_prog_relId'=>$progId,
                ]);
                if (isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])) {
                    $groups->andFilterWhere(['gs_clslvl_relId'=>$clslvlId,]);
                }
                if (isset($_POST['deptId']) && !empty($_POST['deptId'])) {
                    $groups->andFilterWhere(['gs_spec_relId'=>$_POST['deptId']]);
                }
                if (isset($_POST['specId']) && !empty($_POST['specId'])) {
                    $groups->andFilterWhere(['gs_spec_relId'=>$_POST['specId']]);
                }
                $gset = $groups->all();
            }else{
                $gset = WGroupSetup::find()->where(['id'=>$gsetId])->all();
            }


            $gsetIdArray = [];
            if ($gset && count($gset) > 0) {
                foreach ($gset as $key) {
                    $gsetIdArray[] = $key['id'];
                }
            }else{
                echo "ERROR: You are not in correct time for this repert.";
            }
            // course offering
            // ->andWhere([' IS NOT ','coff2det_inst_relId',NULL])

            $coff3 = \backend\models\CourseOffering2Detail::find()
            ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
            ->where(['coff2_acadYear_relId'=>$acadYearId,'coff2_sem_relId'=>$semId])
            ->andWhere(['coff2_group_relId'=>$gsetIdArray])
            
            ->with('coff2detCoff2Rel')
            ->orderBy('course_offering2.coff2_group_relId ASC')
            ->distinct('id')
            ->all();

            if ($coff3 && count($coff3) > 0) {
                $this->GetInstructorCourseAssignmentHistory($coff3,$gset[0]);
            }else{
                echo "<p class='bg-danger'>Course not offered yet.</p>";die();
            }
        }else{
            echo "<p class='bg-danger'>Report generating Parameters empty, Please complete it.</p>";
        }
    }

    private function GetInstructorCourseAssignmentHistory($coff2,$gset){ //courses instructor assignment history
    	$this->__constructor();


        if (!empty($coff2) && count($coff2) > 0) {
        	$c=0;
            $crsum = 0;
            $load = [
                'ta'=>[],
                'inst'=>[]
            ];

            $sem = \backend\models\Semester::findOne(['id'=>$gset['gs_sem_relId']])->semester_name;
            $acdYear = \backend\models\SchoolYear::findOne(['id'=>$gset['gs_acadYear_relId']])->sch_year_desc;

            $dept = isset($_POST['gsetId']) && !empty($_POST['gsetId'])?$gset->gsDeptRel->dept_name:'All';
            $spec = isset($_POST['gsetId']) && !empty($_POST['gsetId'])&& $gset->gsSpecRel?$gset->gsSpecRel->spec_name:'All';
            $prog = $gset->gsProgRel->program_name;
            $adm = $gset->gsAdmRel->admission_name;
            echo "<div class='contianer-fluid' style=' margin:10px;' >";
            echo "<table class='table table-hover table-sm table-bordered table-condensed' >";
            echo "<caption style='text-align:center;'>	
            		<h4>Semester course assignment report</h4>
            		For Semester <i><b>{$sem}</b></i> of <i><b>{$acdYear}</b></i><br>
                    Program : <i><b>{$adm}</b></i> <i><b>{$prog}</b></i> <br>
                    Department : <i><b>{$dept}</b></i> &nbsp;&nbsp;&nbsp;&nbsp; Specialization : <i><b>{$spec}</b></i>
            	</caption>";
            echo "<thead style='background-color:#333; color:white;'>
	            	<th></th>
	            	<th colspan=2 style='text-align:center;'>Course Detail</th>
	            	<th colspan=2 style='text-align:center;'>Instructors Detail</th>
	            	<th colspan=4 style='text-align:center;'>Assignment Detail</th>
            	</thead>";
            echo "<thead style='background-color:#00F99F; text-align:left;'>
	            	<th>#</th>
	            	<th>C. Code</th>
	            	<th>C. Title</th>
	            	<th>Inst. Name</th>
	            	<th>Tech. Asst.</th>
	            	<th>Group</th>
	            	<th>Department</th>
	            	<th>Year.</th>
            	</thead>
            	<tbody>";
        	foreach ($coff2 as $coff) {
        		$gsetId = \backend\models\WGroupSetup::findOne(['id'=>$coff['coff2detCoff2Rel']['coff2_group_relId']]);
                $gname = is_null($coff['coff2detCoff2Rel']['coff2_group_relId'])?'N/A':\backend\models\WGroup::findOne(['id'=>$gsetId->gs_group_relId])->group_name;
                // $ccca = \backend\models\Course::findOne(['id'=>$coff['coff2det_course_relId']]);

                $department = \backend\models\Department::findOne(['id'=>$gsetId->gs_dept_relId])->dept_name;
                $clslvlNum = \backend\models\ClassLevel::findOne(['id'=>$gsetId->gs_clslvl_relId])->clslvl_num;

                $spec = is_null($gsetId->gs_spec_relId)?'':'_'.\backend\models\Specialization::findOne(['id'=>$gsetId->gs_spec_relId])->spec_name;

                $coc = \backend\models\Course::findOne(['id'=>$coff['coff2det_course_relId']]);

                $ta_name = $this->getCoffInstName($coff['coff2det_TA_relId']);
                $inst_name = $this->getCoffInstName($coff['coff2det_inst_relId']);

                $title = $gname."{$spec}";
        		echo "
        			<tr>
        				<td>". ++$c ."</td>
        				<td>". $coc->course_code  ."</td>
        				<td>". $coc->course_title  ."</td>
        				<td>". $inst_name."</td>
        				<td>". $ta_name ."</td>
                        <td title='{$title}'>". $gname ."</td>
                        <td title='{$department}'>". $department ."</td>
                        <td title='{$clslvlNum}'>". $clslvlNum ."</td>
        			</tr>
        		";
                if (!ArrayHelper::keyExists($ta_name,$load['ta'],false)) {
                    $load['ta'][$ta_name] = ['name'=>$ta_name,
                                        'load'=>$coc->course_lab_hour];

                }else if(ArrayHelper::keyExists($ta_name,$load['ta'],false)){
                    $load['ta'][$ta_name] = [
                        'name'=>$ta_name,
                                        'load'=>$load['ta'][$ta_name]['load']+$coc->course_lab_hour];
                }

                // If TA is not assigned, labHour is added to INST.
                if (!ArrayHelper::keyExists($inst_name,$load['inst'],false)) {
                     $load['inst'][$inst_name] = ['name'=>$inst_name,
                                        'load'=>$coc->course_lecture_hour+((2/3)*$coc->course_lab_hour)+((2/3)*$coc->course_tutorial_hour)];

                }else if(ArrayHelper::keyExists($inst_name,$load['inst'],false)){
                     // $load[$inst_name] += $coc->course_crdthr;
                     $load['inst'][$inst_name] = ['name'=>$inst_name,
                                        'load'=>$load['inst'][$inst_name]['load']+$coc->course_lecture_hour+((2/3)*$coc->course_lab_hour)+((2/3)*$coc->course_tutorial_hour)];
                }
        	}
            echo "<tr style='background-color:white; border:0px;'><td colspan=8>&nbsp;</td></tr>
            <tr style='background-color:#ace; text-align:center;'><td colspan=8><h4>Inst. / TA load status</h4></td></tr>";
            echo "<tr style='text-align:center; font-style:italic'>
                    <td colspan=8>Lecture Hr.</td>
                </tr>";
                foreach ($load['inst'] as $Inst) {
                    echo "<tr style=';'>
                            <td colspan=2>&nbsp;</td>
                            <td colspan=3>".$Inst['name']."</td>
                            <td colspan=3>". sprintf("%.02f",$Inst['load']) ."hr/week</td>
                        </tr>";
                }
                echo "<tr style='text-align:center; font-style:italic'>
                    <td colspan=8>Lab Hr.</td>
                    </tr>";
                foreach ($load['ta'] as $ta) {
                    echo "<tr style=''>
                        <td colspan=2>&nbsp;</td>
                        <td colspan=3>".$ta['name']."</td>
                        <td colspan=3>". sprintf("%0.2f",$ta['load']) ."hr/week</td>
                        </tr>";
                }
            
        	echo "</tbody></table></div>";
        }else{
        	echo "No instructor assigned for any course for this semester.";
        }die();
    }

}
