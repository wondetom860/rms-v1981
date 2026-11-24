<?php

namespace frontend\controllers;

use yii;
use yii\helpers\ArrayHelper;
use backend\models\MakeupExamRequest;
use yii\base\ErrorException;

use \common\components\CommonControllerFunctions as CCF;

class SubmitMakeupExamGradeController extends \yii\web\Controller
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
        return $this->render('index');
    }

    public function actionGetApprovedMakeupExamRequests(){
        $this->__constructor();
        $reqs = NULL;
        if(Yii::$app->user->can('DepartmentHead')){
            $reqs = MakeupExamRequest::find()
                    ->where([
                        'mer_acadYear_relId'=>$this->acadYearId,
                        'mer_sem_relId'=>$this->semId,
                        'mer_approved_by'=>Yii::$app->user->identity->id,
                    ])
                    ->andWhere(['IS NOT', 'mer_approved',NULL])
                    ->all();
        }else if (Yii::$app->user->can('Registrar')) {
           $reqs = MakeupExamRequest::find()
                    ->where([
                        'mer_acadYear_relId'=>$this->acadYearId,
                        'mer_sem_relId'=>$this->semId,
                    ])
                    ->andWhere(['IS NOT','mer_registrarApprove_status',NULL])
                    ->all();
        }
        // \
        
            if ($reqs) {
                echo "<table class='table table-striped table-condensed table-xs table-bordered table-hover' style='font-size:10pt;'>";
                echo "<caption><p class='bg-success'> <i class='bg-success'>". count($reqs) ."</i> Course re-assessment Requests made in this semester.</p></caption>";
                echo "<thead style='background-color:#ace;'>
                <th>#</th>
                <th>RegId</th>
                <th>Full Name</th>
                <th>Course Title</th>
                <th>Registrar</th>
                <th>HOD</th>
                <th>bf. Grade</th>
                <th>af. Grade</th>
                </thead><tbody>";
                $count = 0;
            foreach ($reqs as $std) {
                $student = \backend\models\Student::findOne(['id'=>$std['mer_std_relId']]);
                $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
                $rap = is_null($std['mer_registrarApprove_status'])?'Pending':($std['mer_registrarApprove_status'] == 1?'Approved':'Rejected');
                $hop = is_null($std['mer_status'])?'Pending...':($std['mer_status'] == 1?'Approved':'Rejected');

                $afg = is_null($std['mer_aft_grade_relId'])?'-':\backend\models\GradeScale::findOne(['id'=>$std['mer_aft_grade_relId']])->gs_ltr_grade;
                //$grp = \backend\models\WGroupSetup::findOne(['id'=>$std['ah_addWithGroup_relId']]);

                //  <td>". \backend\models\WGroup::findOne(['id'=>$grp->id])->group_name ."</td>
                // <td>
                echo "<tr>
                    <td>". ++$count ."</td>
                    <td>". $student->std_regId ."</td>
                    <td>". $usr->first_name.' '. $usr->middle_name ."</td>
                    <td>". \backend\models\Course::findOne(['id'=>$std['mer_course_relId']])->course_title ."</td>
                    <td>". $rap."</td>
                    <td>". $hop."</td>";

                echo "<td>". \backend\models\GradeScale::findOne(['id'=>$std['mer_bef_grade_relId']])->gs_ltr_grade ."</td>
                    <td>". $afg ."</td>;
                </tr>";

              
            }
            echo "</tbody></table>";
            }else{
                echo "<p class='bg-danger'>No course makeup-request approved.</p>";
            }
    }


    public function actionSaveMark(){
        // courseId:courseId,
        // stdId:stdId,
        // catagoryId:catagoryId,
        // imaId:imaId,

        $courseId = isset($_POST['courseId']) && !empty($_POST['courseId'])?intval($_POST['courseId']):die('Uknown Course');
        $stdId = isset($_POST['stdId']) && !empty($_POST['stdId'])?intval($_POST['stdId']):die('Uknown Student');
        $catagoryId = isset($_POST['catagoryId']) && !empty($_POST['catagoryId'])?intval($_POST['catagoryId']):die('Uknown Assessment Catagory');
        $imaId = isset($_POST['imaId']) && !empty($_POST['imaId'])?intval($_POST['imaId']):die('Uknown Assessment Mark distribution...');
        $markGiven = isset($_POST['markEntered']) && !empty($_POST['markEntered'])?floatval($_POST['markEntered']):die('Given mark empty');


        $contAssessObject = new \backend\models\ContinuousAssessment2();
        if (Yii::$app->user->can('DepartmentHead')) {

            $contAssessUniqueObject = $contAssessObject->getContAssessUniqueId($courseId, $stdId,$catagoryId,$imaId);

            if ($contAssessUniqueObject && count($contAssessUniqueObject) > 0) {
               $stat = $contAssessObject->updateFinalExamMark($contAssessUniqueObject,$markGiven,$courseId);

               if (is_null($stat)) {
                   echo "<p class='bg-danger'>Error Occured, Please retry later.</p>";
               }else{
                    if (is_bool($stat) && $stat == TRUE) {
                        return $this->redirect('/approve-grade')->send();
                    }else{
                        if (is_object($stat) && isset($stat->gs_ltr_grade)) {
                            return $this->redirect('/approve-grade')->send();
                        }else{
                            echo "<p class='bg-danger'>Error Occured, {$stat}.</p>";
                        }
                    }
               }
            }else{
                echo "<p class='bg-danger'>Such coninous assessment entry not found.....</p>";
            }
        die();
        }

    }

    // public function actionFindMissingAssessment(){
    //     $courseId = isset($_POST['courseId']) && !empty($_POST['courseId'])?intval($_POST['courseId']):die('Course Uknown');
    //     $stdId = isset($_POST['stdId']) && !empty($_POST['stdId'])?intval($_POST['stdId']):die('Course Student');
    //     $coffDetId = isset($_POST['coffDetId']) && !empty($_POST['coffDetId'])?intval($_POST['coffDetId']):die('Course Student');

    //     $ima = new \backend\models\InstanceMarkAssignment();


    // }

    public function actionLoadContAssessReport(){
        $srd2Id = isset($_POST['srdId']) && !empty($_POST['srdId'])?intval($_POST['srdId']):die('Error!!.');
        $this->__constructor();
        $smeregDet = \frontend\models\SemesterRegistration2Detail::findOne($srd2Id);

        $stdId = \frontend\models\SemesterRegistration2::findOne($smeregDet->srd2_semreg_relId)->semreg_std_relId;
        $std = \backend\models\Student::findOne($stdId);
        $user = \common\models\User1::findOne($std->std_user_relId);

        $course = \backend\models\Course::findOne($smeregDet->srd2_course_relId);

        $model = new \backend\models\ContAssessCatagory2();
        $catagory = $model->getCourseCatagory($course->id);
        $contAssessObject = new \backend\models\ContinuousAssessment2();
        $instanceModel = new \backend\models\InstanceMarkAssignment();
        $instances = $instanceModel->getAssessmentInstances2($course->id,1);


        $table = "";
        if ($instances && count($instances) > 0) {
            // style='background-color:#333; color:white;'
            $table = "<table class='table table-sm table-bordered table-condensed'>";
            $table .= "<caption>
                        <h4 class='bg-success' style='text-align:center; text-decoration:underline;'>Continous assessment mark submission report.</h4>
                        <b>Course Code</b> <u>{$course->course_code}</u><br>
                        <b>Course Title</b> <u>{$course->course_title}</u><br>
                        <p class='bg-info'>Reg. ID: {$std->std_regId} &nbsp;&nbsp;&nbsp; Name: {$user->first_name} {$user->middle_name}</p>
                        </caption>";
            $table .= "<thead style='background-color:#333; color:white;'>
                        <th>#</th>
                        <th>Assessment Name</th>
                        <th>Mark Obtained</th>
                        <th>Total Mark</th>
                        <th>Assessed On</th>
                    </thead><tbody>";
                    $count = 0;
                    $asessDate = NULL;
            foreach ($instances as $key) {
                // $instance = $model22->getInstance($key->id,NULL);

                $mark = $contAssessObject->getStudentMark($course->id, $stdId,$key->ima_catagorySetup_relId,$key->id);
                if ($mark && count($mark) > 0) {
                    continue;
                }else{
                    // $asessDate = date_format(date_create($mark->ca_created_at), 'D d M Y');
                    $mm = "  <input type='number' id = 'markInput' max='{$key->ima_possiblePoint}' min=0 scale='0.01' > <button onclick='saveMark({$course->id}, {$stdId},{$key->ima_catagorySetup_relId},{$key->id})'>Save</button> ";
                }

            $table .= "<tr>
                        <td>".++$count."</td>
                        <td style='text-align:left;'>{$key->ima_instanceName}</td>
                        <td>{$mm}</td>
                        <td>{$key->ima_possiblePoint}</td>
                        <td>{$asessDate}</td>
                    </tr>";
            }
            $table .= "</tbody></table>";
            echo "{$table}";die();
        }else{
            echo "<p class='bg-warning'>No assessment record available.</p>";die();
        }

        // echo "{$srd2Id}";
    }
    // Get course re-assessment requests made in this semester.
    public function actionGetMakeupExamRequests(){  
    	$this->__constructor();
        $reqs = NULL;
        if(Yii::$app->user->can('DepartmentHead')){

            $ghInst = \backend\models\Instructor::findOne(['inst_user_relId'=>Yii::$app->user->identity->id]);
            $instDept = $ghInst->inst_dep_relId;


            $reqs = MakeupExamRequest::find()
                ->where([
                    'mer_acadYear_relId'=>$this->acadYearId,
                    'mer_sem_relId'=>$this->semId,
                    'mer_approved'=>1,
                    'mer_registrarApprove_status'=>1,
                ])
                ->innerJoin('course','makeup_exam_request.mer_course_relId = course.id')
                ->andWhere(['course.course_dept_relId'=>$instDept])
                ->all();
        }else if(Yii::$app->user->can('Registrar')){
             $reqs = MakeupExamRequest::find()
                ->where([
                    'mer_acadYear_relId'=>$this->acadYearId,
                    'mer_sem_relId'=>$this->semId,
                    'mer_approved'=>1,
                    'mer_registrarApprove_status'=>1,
                ])
                ->all();
        }
    	

        if ($reqs && count($reqs) > 0) {
            echo "<table class='table table-striped table-condensed table-xs table-bordered table-hover' style='font-size:9pt;'>";
            
            echo "<thead style='background-color:#ace;'>
            <th>#</th>
            <th>RegId</th>
            <th>Full Name</th>
            <th>Student Dept.</th>
            <th>Batch</th>
            <th>Course Title</th>
            <th>Course Added</th>
            <th colspan = 2 style='text-align:center;'>option</th>
            </thead><tbody>";
            $option = '';
            // if (Yii::$app->user->can('DepartmentHead')) {
            //     $option = "<button class='btn btn-primary btn-xs btn-flat' title='Show Assessment History' onclick='showAssessmentModal({$std->id},{$cc->id})'>Show</button>";
            // }
            $count = 0;
            foreach ($reqs as $std) {

                $srd2 = \frontend\models\SemesterRegistration2Detail::findOne($std->mer_srd2_relId);
                $gset = NULL;
                $student = \backend\models\Student::findOne(['id'=>$std['mer_std_relId']]);
                $usr = \backend\models\User::findOne(['id'=>$student->std_user_relId]);
                $cc = \backend\models\Course::findOne(['id'=>$std['mer_course_relId']]);
                $cadda = 'NO';
                $dept = 'Uknown';
                $clslvl = 'Uknown';


                if ($srd2->srd2_coadd_flag == 1) {
                    $cadda = "YES";
                    $gset = \backend\models\AddHistory::findOne([
                        'ah_srd2_relId'=>$srd2->id,
                        'ah_course_relId'=>$cc->id,
                        'ah_std_relId'=>$student->id,
                        'ah_acadYear_relId'=>$this->acadYearId,
                        'ah_sem_relId'=>$this->semId
                    ])->ah_addWithGroup_relId;
                }else{
                    $gset = CCF::currentStudentGroup($student);
                }

                if ($gset && count($gset) > 0) {
                    $dept = \backend\models\Department::findOne($gset->gs_dept_relId)->dept_name;
                    $clslvl = \backend\models\ClassLevel::findOne($gset->gs_clslvl_relId)->clslvl_num;
                }

                if (Yii::$app->user->can('DepartmentHead')) {
                    $option = "<button class='btn btn-primary btn-xs btn-flat' title='Show Assessment History' onclick='showAssessmentModal({$std->id},{$cc->id},{$srd2->id})'>Show</button>";
                }

                echo "<tr>
                <td>". ++$count ."</td>
                <td>". $student->std_regId ."</td>
                <td>". $usr->first_name.' '. $usr->middle_name ."</td>
                <td>{$dept}</td>
                <td style='text-align:center;'>Year - {$clslvl}</td>
                <td style='text-align:center;'>{$cadda}</td>
                <td>". $cc->course_title ."</td>
                <td style='text-align:center;'>
                    {$option}
                </td>
                </tr>";
                // ++$count;
            }
            echo "</tbody>";
            // echo "<tfooter><p class='bg-success'> <i class='bg-danger'>". $count ."</i> Requests.</p></tfooter>";
            echo "</table>";
            }else{
                die("<p class='bg-danger'>No request.</p>");
            }
    }
}

