<?php

namespace frontend\controllers;
use \common\components\CommonControllerFunctions as CCF;
use Yii;

class CourseOffering2ExtController extends \yii\web\Controller
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
    	$model = new \backend\models\CourseOffering2();
    	if (\Yii::$app->user->can('CEP_DEPT')) {
    		return $this->render('index',['model'=>$model]);
    	}else if(\Yii::$app->user->can('Registrar')){
            return $this->render('index_registrar');
        }
        // return $this->render('index',['model'=>$model]);
    }

    public function actionApproveOffering(){
        $id = isset($_POST['id']) && !empty($_POST['id']) ? intval($_POST['id']):die('Invalid Entry!');
        $action = isset($_POST['action']) && !empty($_POST['action']) ? intval($_POST['action']):NULL;

        if (\Yii::$app->user->can('Registrar')) {
            // $exists = \frontend\models\SemesterRegistration2Detail::findOne(['srd2_coff2det_relId'])
            return \Yii::$app->db->createCommand()
                ->update('course_offering2_detail',
                    [
                        'coff2det_registrarApproved'=>$action,
                        'coff2det_registrarApproved_at' => date('Y-m-d h:i:s'),
                        'coff2det_registrarApproved_by' => \Yii::$app->user->identity->id
                    ],
                    [
                        'coff2det_coff2_relId'=>$id
                    ])
                ->execute();
            // die();
        }else{
            echo "UN_PRIVILLEGED_OPERTAION_ERROR!!";
            die();
        }

    }
    public function actionOfferCourses(){//OfferCourses

        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId'])? intval($_POST['acadYearId']) : die('Academic Year Not Known.');
        $semId = isset($_POST['semId']) && !empty($_POST['semId'])? intval($_POST['semId']) : die('Semester Not Known.');
        $admId = isset($_POST['admId']) && !empty($_POST['admId'])? intval($_POST['admId']) : die('Admission Not Known.');
        $progId = isset($_POST['progId']) && !empty($_POST['progId'])? intval($_POST['progId']) : die('Program Not Known.');
        $deptId = isset($_POST['deptId']) && !empty($_POST['deptId'])? intval($_POST['deptId']) : die('Department Not Known.');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])? intval($_POST['clslvlId']) : die('Class Year Not Known.');
        $specId = isset($_POST['specId']) && !empty($_POST['specId'])? intval($_POST['specId']) : NULL;

        $cocatLists = isset($_POST['catList']) && !empty($_POST['catList'])? $_POST['catList'] : die('Course List Empty.');

        $model = new \backend\models\CourseOfferingTemp();

        // var_dump($model);die();

        if($model->checkIfOffered($acadYearId,$semId,$admId,$progId,$deptId,$clslvlId,$specId) > 0){
            // check if anychange exists on courses List.

            $cocats = $model->getOfferedCourses($acadYearId,$semId,$admId,$progId,$deptId,$clslvlId,$specId);

            // var_dump($cocats);die();

            if ($cocats[2] == 0) {
                $newList = array_merge($cocats[0],json_decode($cocatLists));

                return Yii::$app->db->createCommand()
                    ->update('course_offering_temp',
                    [
                        'cot_cocat_IDS'=>json_encode($newList),
                    ],
                    [
                        'id'=>$cocats[1]
                    ])
                ->execute();
            }

            return "Offering Completed, No Further action Allowed. Please Contact registrar if you have any complain.";

        }else{
            $model->cot_acadYear_relId = $acadYearId;
            $model->cot_sem_relId = $semId;
            $model->cot_adm_relId = $admId;
            $model->cot_prog_relId = $progId;
            $model->cot_dept_relId = $deptId;
            $model->cot_clslvel_relId = $clslvlId;
            $model->cot_spec_relId = $specId;
            $model->cot_cocat_IDS = $cocatLists;

            $model->cot_created_by = Yii::$app->user->identity->id;
            $model->cot_created_at = date('Y-m-d h:i:s');
            // $model->cot_cocat_IDS = $cocatLists;
            // $model->cot_cocat_IDS = $cocatLists;

            if($model->save()){
                return 1;
            }else{
                var_dump($model->getErrors());
                die();
            }
        }

        // die();  
    }

    // get un-approved courses
    public function actionGetCoursesForRegistrarApproval(){//get_courses_for_registrar_approval
        // Alert only for this academic Year
        $this->__constructor();
        $courses = \backend\models\CourseOffering2Detail::find()
            ->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
            ->where(['OR',
                ['course_offering2_detail.coff2det_registrarApproved'=>NULL],
                ['IS NOT','course_offering2_detail.coff2det_registrarApproved_by',NULL]//incase if the registrar wish to change approval status!
            ])
            ->groupBy('coff2det_coff2_relId')
            ->all();
	   $wreport = "";
        if ( $courses) {
            $crsum2 = 0;
            $wreport .= "<table class='table table-condensed table-bordered table-hover' style='font-size:10pt;'>";
            $wreport .= "<thead style='background-color:#8D0;'>
                <th style='border:1px solid black;'>#</th>
                <th style='border:1px solid black;'>Admission</th>
                <th style='border:1px solid black;'>Program</th>
                <th style='border:1px solid black;'>Department</th>
                <th style='border:1px solid black;'>Spec.</th>
                <th style='border:1px solid black;'>Class L.</th>
                <th style='border:1px solid black;'>Group</th>
                <th style='border:1px solid black;'>Courses</th>";
            if (\Yii::$app->user->can('Registrar')) {
                $wreport .= "<th style='border:1px solid black; text-align:center;'>Option</th>";
            }
            $wreport .= "
            </thead>
            <tbody>";
            $count = 0;
            foreach ($courses as $key) {
                $c = $this->getCourses($key->coff2det_coff2_relId);
                $gset = $key->coff2detCoff2Rel->coff2GroupRel;

                $admission = $gset->gsAdmRel->admission_name;
                $program = $gset->gsProgRel->program_name;
                $dept = $gset->gsDeptRel->dept_name;
                $spec = is_null($gset->gsSpecRel)?'-':$gset->gsSpecRel->spec_name;
                $clslvl = $gset->gsClslvlRel->clslvl_num;
                $groupName = $gset->gsGroupRel->group_name;
                $wreport .= "<tr>
                    <td style='border:1px solid black;'>".++$count."</td>
                    <td style='border:1px solid black;'>{$admission}</td>
                    <td style='border:1px solid black;'>{$program}</td>
                    <td style='border:1px solid black;'>{$dept}</td>
                    <td style='border:1px solid black;'>{$spec}</td>
                    <td style='border:1px solid black;'>{$clslvl}</td>
                    <td style='border:1px solid black;'>{$groupName}</td>
                    <td style='border:1px solid black;'>{$c}</td>
                ";
                if ($key->coff2det_registrarApproved != 1) {
                    $wreport .= "<td style='text-align:center; vertical-align:middle; border:1px solid black;'>
                        <button class='btn btn-success btn-xs'  onclick='approveOffer({$key->coff2det_coff2_relId},1)'>Approve</button>
                        </td>";
                }else{
                    $wreport .= "<td style='text-align:center; vertical-align:middle; border:1px solid black;'>
                        <button class='btn btn-warning btn-xs'  onclick='approveOffer({$key->coff2det_coff2_relId},0)'>Change</button>
                        </td>";
                }
                $wreport .= "
                    </tr>";
            }
            return $wreport . "</tbody></table>";
        }
    }

    private function getCourses($key){

        $coffs = \backend\models\CourseOffering2Detail::find()->where(['coff2det_coff2_relId'=>$key])->all();
        
        $table = "";
        if ($coffs && count($coffs) > 0) {
            $crsum2 = 0;
            $table .= "<table class='table table-condensed table-bordered table-hover' style='font-size:7pt;'>";
            $table .= "<thead style='background-color:#8D0;'>
                <th style='border:1px solid black;'>#</th>
                <th style='border:1px solid black;'>Course Code</th>
                <th style='border:1px solid black;'>Corse Title</th>
                <th style='border:1px solid black;'>Crdt. Hr.</th>";
            $table .= "</thead><tbody>";
                $count = 0;
           foreach ($coffs as $key2) {
                $c = $key2->coff2detCourseRel;
                $crsum2 += $c->course_crdthr;
                $table .= "<tr>
                    <td style='border:1px solid black;'>".++$count."</td>
                    <td style='border:1px solid black;'>{$c->course_code}</td>
                    <td style='border:1px solid black;'>{$c->course_title}</td>
                    <td style='font-weight:bold; text-align:center; border:1px solid black;'>{$c->course_crdthr}</td>
                    </tr>
                    ";
           }
           $table .= "<tr style='font-weight:bold; text-align:center;'>
                    <td colspan = 3  style='border:1px solid black;'>Total Credit</td>
                    <td style='text-decoration:underline; border:1px solid black;'>{$crsum2}</td>
                </tr>";
            $table .= "</tbody></table>";
        }else{
            $table = "No Course Found.";
        }
        return $table;
    }


    public function actionGetGroupCourses(){
    	$gsetId = isset($_POST['grp']) && !empty($_POST['grp']) ? intval($_POST['grp']):die('Invalid Entry!');

    	$courses = \backend\models\CourseOffering2Detail::find()
    		->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
    		->where(['course_offering2.coff2_group_relId'=>$gsetId])
    		->all();

    	if ($courses && count($courses) > 0) {
            // $cocats = $cocats->all();
            $crsum2 = 0;
            echo "<table class='table table-condensed table-bordered table-hover' style='font-size:8pt;'>";
            echo "<thead style='background-color:#8D0;'>
                <th style='border:1px solid black;'>#</th>
                <th style='border:1px solid black;'>Course Code</th>
                <th style='border:1px solid black;'>Corse Title</th>
                <th style='border:1px solid black;'>Crdt. Hr.</th>";
            if (\Yii::$app->user->can('CEP_DEPT') || \Yii::$app->user->can('Admin')) {
                echo "<th style='border:1px solid black;'>Cancel</th>";
            }
            echo "
            </thead>
            <tbody>";
            $count = 0;
            foreach ($courses as $key) {
                $c = \backend\models\Course::findOne(['id'=>$key['coff2det_course_relId']]);
                $crsum2 += $c->course_crdthr;
                echo "<tr>
                    <td style='border:1px solid black;'>".++$count."</td>
                    <td style='border:1px solid black;'>{$c->course_code}</td>
                    <td style='border:1px solid black;'>{$c->course_title}</td>
                    <td style='font-weight:bold; text-align:center; border:1px solid black;'>{$c->course_crdthr}</td>
                    ";
                if (\Yii::$app->user->can('CEP_DEPT') && $key->coff2det_registrarApproved != 1) {
                    echo "<td style='text-align:center; border:1px solid black;'>
                    	<button class='btn btn-warning btn-xs'  onclick='deleteCourse({$key->id})'>Cancel</button>
                    	</td>";
                }else{
                	echo "<td  style='border:1px solid black;'>Offered.</td>";
                }
                echo "
                    </tr>";
            }
            echo "<tr style='font-weight:bold; text-align:center;'>
            		<td colspan = 3  style='border:1px solid black;'>Total Credit</td>
            		<td style='text-decoration:underline; border:1px solid black;'>{$crsum2}</td>
            	</tr>";
            echo "</tbody></table>";
        }else{
            echo "<span style='color:#ace; font-style:italic;'>No History found for your selection.</span>";
        }
    }

    public function actionDeleteCourse(){//DeleteCourse = delete-course
        $coffdetId = isset($_POST['id']) && !empty($_POST['id'])? intval($_POST['id']):die('Invalid Entry');

        $coffDetObj = \backend\models\CourseOffering2Detail::findOne($coffdetId);

        if ($coffDetObj) {
            if ($coffDetObj->coff2det_registrarApproved != 1) {
                echo $coffDetObj->delete();
            }else{
                echo "Course Offering already approved by Registrar.";
            }
        }else{
            echo "No such offering Entry found.";
        }
        die();
    }

}
