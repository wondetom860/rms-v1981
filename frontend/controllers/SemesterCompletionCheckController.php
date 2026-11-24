<?php

namespace frontend\controllers;

use Yii;
use backend\models\StudentSemesterStatusHistory;
use backend\models\WGroupSetup;
use frontend\models\SemesterRegistration2Detail;
use backend\models\Student;
use backend\models\User;
use backend\models\Course;
use backend\models\ContAssessCatagory2;
use \backend\models\InstanceMarkAssignment;
use \backend\models\ContinuousAssessment2;
use \common\components\CommonControllerFunctions as CCF;

class SemesterCompletionCheckController extends \yii\web\Controller {

    public $acadYearId;
    public $semId;

    protected function __constructor() {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }


    public function actionLoadContAssessReport(){
        $srd2Id = isset($_POST['srdId']) && !empty($_POST['srdId'])?intval($_POST['srdId']):die('Error!!.');
        $this->__constructor();
        $smeregDet = SemesterRegistration2Detail::findOne($srd2Id);

        $stdId = \frontend\models\SemesterRegistration2::findOne($smeregDet->srd2_semreg_relId)->semreg_std_relId;
        $std = \backend\models\Student::findOne($stdId);
        $user = \backend\models\User::findOne($std->std_user_relId);

        $course = Course::findOne($smeregDet->srd2_course_relId);

        $model = new ContAssessCatagory2();
        $catagory = $model->getCourseCatagory($course->id);
        $contAssessObject = new ContinuousAssessment2();
        $instanceModel = new InstanceMarkAssignment();
        $instances = $instanceModel->getAssessmentInstances2($course->id);


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
                    $mm = $mark->ca_markObtained;
                    $asessDate = date_format(date_create($mark->ca_created_at), 'D d M Y');
                }else{
                    $mm = '-';
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
    public function actionIndex() {
        if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('DepartmentHead') || Yii::$app->user->can('Admin') || Yii::$app->user->can('clerk')) {
            $model = new StudentSemesterStatusHistory(); 
            return $this->render('index_reg_HoD', ['model' => $model]);
        }
        return $this->render('index');
    }

    public function actionReport() {
        // admId:admId,
        // progId:progId,
        // clslvlId:clslvlId,
        // deptId:deptId,  
        // $this->__constructor();     
        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Please select Admission type..');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Please select Program..');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : NULL;
        $deptId = isset($_POST['deptId']) && !empty($_POST['deptId']) ? intval($_POST['deptId']) : die('Please select department');
        
        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die("Please select Academic Year");
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die("Please select Semester");

        $gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId']) ? intval($_POST['gsetId']) : die("Please select Group");

        // here we are going to display students who didnot completed their semester coure assessment.
        // mostly this can be obtained from semester registration record.
        // first get thos groups per the sselection.

        $gsIds = [];

        // $gmodel = new 
        $gsets = WGroupSetup::find()->where(['id'=>$gsetId])->all();

        foreach ($gsets as $key) {
            $gsIds[] = $key->id;
        }
        // die();
        $table = "";
        $tableHead = "";
        $invalidGradeList = \backend\models\GradeScale::find()
                ->where(['gs_valid' => 0])
                ->all();


        $invalidGradeListArray = [];
        if ($invalidGradeList && count($invalidGradeList) > 0) {
            foreach ($invalidGradeList as $key4) {
                $invalidGradeListArray[] = $key4->id;
            }
        }
        // echo "<pre>";
        // print_r($invalidGradeListArray);die();
        $recordAvaliable = FALSE;
        if ($gsets && count($gsets) > 0) {

            $adm = \backend\models\Admission::findOne($admId);
            $prog = \backend\models\Program::findOne($progId);
            $dept = \backend\models\Department::findOne($deptId);
            $Sem = \backend\models\Semester::findOne($semId);
            $acadYear = \backend\models\SchoolYear::findOne($acadYearId)->sch_year_desc;
            $clslvl = is_null($clslvlId)?'All':\backend\models\ClassLevel::findOne($clslvlId)->clslvl_num;
            $tableHead .= "<table class='table table-hover table-sm table-bordered table-condensed' style='font-size:9pt;'>";
            $tableHead .= "<caption style='text-align:center; font-weight:bold;'>
                    <h4 style='text-decoration:underline; text-align:center;'>Student Semester completion check</h4>
                    Admission:<u><i>{$adm->admission_name}</i></u>&nbsp;&nbsp;&nbsp;
                    Program:<u><i>{$prog->program_name}</i></u>&nbsp;&nbsp;&nbsp;
                    Department:<u><i>{$dept->dept_name}</i></u>&nbsp;&nbsp;&nbsp;
                    Class Level:<u><i>{$clslvl}</i></u>&nbsp;&nbsp;&nbsp;Semester:<u><i> {$Sem->semester_name} </i></u> of <u><i> {$acadYear}</i></u><br>
                </caption>";
            $tableHead .= "<thead style='background-color:#8D5;'>
                    <th>#</th>
                    <th>REG. ID</th>
                    <th>Student Name</th>
                    <th>Department</th>
                    <th>Specialization</th>
                    <th>Yr.</th>
                    <th>Course Code</th>
                    <th>Couse Title</th>
                    <th>Grade</th>
                    <th>Option</th>
                </thead><tbody>";
            $count = 0;
            $Option = NULL;
            $clslvls = '';
            $displayedSrds = [];
            foreach ($gsets as $key) {
                // get students under this group and those who enrolled courses, but their ssemsetr completion is under trouble.
                
                // $gsetStudentsArray = [];
                // $gsetStudents = \backend\models\WGroupAssignment::find()->where(['gas_gset_relId'=>$key->id])->all();

                // if ($gsetStudents && count($gsetStudents) > 0) {
                //     foreach ($gsetStudents as $GASkey) {
                //         $gsetStudentsArray[] = $GASkey->gas_std_relId;
                //     }
                // }else{
                //     continue;
                // }
                $enrolls = SemesterRegistration2Detail::find()
                        ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                        ->where([
                            'semreg_acadYear_relId' => $key->gs_acadYear_relId,
                            'semreg_semester_relId' => $key->gs_sem_relId
                        ])
                        ->andWhere(['srd2_group_relId'=>$gsIds])
                        ->andWhere(['OR',
                            ['srd2_grade_relId' => $invalidGradeListArray],
                            ['srd2_grade_relId' => NULL]
                        ])
                        
                        ->with('srd2SemregRel')
                        ->all();

                    if (is_null($clslvlId)) {
                        // $clslvlId = \backend\models\WGroupSetup::findOne($key->srd2_group_relId)->gs_clslvl_relId;
                        $clslvls = \backend\models\ClassLevel::findOne($key->gs_clslvl_relId)->clslvl_num;

                    }else{
                        $clslvls = $clslvl;
                    }

                // "SELECT `semester_registration_2_detail`.* FROM `semester_registration_2_detail` INNER JOIN `semester_registration_2` ON semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId WHERE ((`semreg_gradeCompleted`=0) OR (`semreg_SGPA`=0)) AND ((`semreg_acadYear_relId`=1) AND (`semreg_semester_relId`=2)) AND (`srd2_group_relId`=3) AND ((`srd2_grade_relId` IN (9, 10)) OR (`srd2_gerade_relId` IS NULL))"
                 // echo "<pre><hr>";
                 // var_dump($enrolls);
                $ad = NULL;
                if ($enrolls && count($enrolls) > 0) {

                    // echo "1";
                    // die();

                    foreach ($enrolls as $smeregDet) {
                        $course = Course::findOne($smeregDet->srd2_course_relId);
                        if (in_array($smeregDet->id, $displayedSrds)) {
                            continue;
                        }else{
                            $displayedSrds[] = $smeregDet->id;
                        }
                        // check if assessment is completed and the student missed any of them.
                        // I have a controller function for checking this.

                        // if (CCF::AssessmentCompleted($acadYearId, $semId, $course->id)) {


                        // } 
                        $std = Student::findOne($smeregDet['srd2SemregRel']['semreg_std_relId']);
                        $spec = is_null($std->std_spec_relId) ? '-' : \backend\models\Specialization::findOne($std->std_spec_relId)->spec_name;
                        $user = User::findOne($std->std_user_relId);
                        $ltrGrade = is_null($smeregDet->srd2_grade_relId)?'N/C':\backend\models\GradeScale::findOne($smeregDet->srd2_grade_relId)->gs_ltr_grade;

                        if (!is_null($smeregDet->srd2_coadd_flag)) {
                            $ad = "<b>(Ad.)</b>";
                        }
                        if ($smeregDet->srd2GroupRel->gsAdmRel->id != $admId) {
                            continue;
                        }

                        if ($smeregDet->srd2GroupRel->gsProgRel->id != $progId) {
                            continue;
                        }

                        // check if the student requested re-assessment for the course.
                        $reAssRequested = \backend\models\ReassessmentHistory::findOne([
                            'ras_acadYear_relId'=>$acadYearId,
                            'ras_sem_relId'=>$semId,
                            'ras_std_relId'=>$std->id,
                            'ras_srd2_relId'=>$smeregDet->id,
                            'ras_course_relId'=>$course->id,
                        ]);

                        if ($reAssRequested && count($reAssRequested) > 0) {
                            $ltrGrade = "<span style='color:red; font-style:italic'>Requested for Reassessment.</span>";
                        }

                        if (Yii::$app->user->can('DepartmentHead')) {
                            // options for HoD. [scaleUp,change to I, make F]
                            $Option = "<select title='Change Grade of this course for the student.' onchange='changeByHoD({$smeregDet->id},$(this).val(),{$std->id})'>
                                        <option value=''> - </option>
                                        <option value='I'> Change to 'I' </option>
                                        <option value='F'> Change to 'F' </option>
                                        <option value='su' title='Under decision, waiting...'> ScaleUp </option>
                                    </select>";
                            if ($ltrGrade == 'I') {
                                $Option = "<select title='Change Grade of this course for the student.' onchange='changeByHoD({$smeregDet->id},$(this).val(),{$std->id})'>
                                        <option value=''> - </option>
                                        <option value='F'> Change to 'F' </option>
                                        <option value='su' title='Under decision, waiting...'> ScaleUp </option>
                                    </select>";
                            }
                        }else if (Yii::$app->user->can('Registrar')) {
                            $Option = "<select title='Change Grade of this course for the student.' onchange='changeByRegistrar({$smeregDet->id},$(this).val(),{$std->id})'>
                                        <option value=''> - </option>
                                        <option value='F'> Change to 'F' </option>
                                    </select>";
                        }
                        // $gn = \backend\models\WGroup::findOne(->gs_group_relId);
                        $table .= "<tr>
                                    <td>" . ++$count . "</td>
                                    <td>{$std->std_regId}</td>
                                    <td>{$user->first_name} {$user->middle_name}</td>
                                    <td style='text-align:center;'><b>{$dept->dept_code}({$smeregDet->srd2GroupRel->gsGroupRel->group_name})</b></td>
                                    <td>{$spec}</td>
                                    <td>{$smeregDet->srd2GroupRel->gsClslvlRel->clslvl_num}{$ad}</td>
                                    <td>{$course->course_code}</td>
                                    <td>{$course->course_title}</td>
                                    <td title='assessment not completed for this student, there is missing assessment entry.' style='text-align:center;color:red'>{$ltrGrade}</td>
                                    <td>{$Option}</td>
                                </tr>";
                        $recordAvaliable = TRUE;
                    }
                }
            }

            if ($recordAvaliable) {
                $table = $tableHead . $table;
                $table .= "</tbody></table>";
            } else {
                $table .= "<p class='bg-success'>No student with semester completion error found yet.</p>";
            }

            echo "{$table}";
            die();
        } else {
            echo "<p class='bg-danger'>No group setup made for your selection.</p>";
        }
    }

    public function actionChangeGrade(){
        $semregDetId = isset($_POST['srd2Id']) && !empty($_POST['srd2Id'])?intval($_POST['srd2Id']):die('Invalid Semester Registration entry,');
        $action = isset($_POST['action']) && !empty($_POST['action'])?$_POST['action']:die('Uknown action selected.');
        $stdId = isset($_POST['stdId']) && !empty($_POST['stdId'])?$_POST['stdId']:die('Uknown Student, Please select One....');

        if (($action == 'I' || $action == 'F') && Yii::$app->user->can('DepartmentHead')) {
            // get gradeScale Id.
            $gs = \backend\models\GradeScale::find()->where(['gs_ltr_grade'=>$action])->one();
            if (!($gs && count($gs) > 0)) {
                die('Invalid Grade.');
            }
            $exists = \frontend\models\SemesterRegistration2Detail::findOne($semregDetId);
            $result = NULL;
            if ($exists && count($exists) > 0) {
                $gset = \backend\models\WGroupSetup::findOne($exists->srd2_group_relId);
                if ($action == 'I') {
                    $result = Yii::$app->db->createCommand()
                        ->update('semester_registration_2_detail',
                            ['srd2_grade_relId'=>$gs->id,'srd2_gradeApproved'=>NULL],
                            ['id'=>$semregDetId])
                        ->execute();
                }else{
                    $result = Yii::$app->db->createCommand()
                        ->update('semester_registration_2_detail',
                            ['srd2_grade_relId'=>$gs->id],
                            ['id'=>$semregDetId])
                        ->execute();
                }
                if ($result) {
                    CCF::checkIfGradeCompleted($stdId,$gset);//pass student Id.
                    echo 1;
                }else{
                    die("<p class='bg-warning'>Updating Error, Please  retry again...!!</p>");
                }
            }else{
                die("<p class='bg-warning'>Semester Registration Entry Error!!</p>");
            }
        }else if (($action == 'F') && Yii::$app->user->can('Registrar')) {
            $gs = \backend\models\GradeScale::find()->where(['gs_ltr_grade'=>$action])->one();
            if (!($gs && count($gs) > 0)) {
                die('Invalod Grade.');
            }
            $exists = \frontend\models\SemesterRegistration2Detail::findOne($semregDetId);
            if ($exists && count($exists) > 0) {
                $gset = \backend\models\WGroupSetup::findOne($exists->srd2_group_relId);
                $result = Yii::$app->db->createCommand()
                    ->update('semester_registration_2_detail',
                        ['srd2_grade_relId'=>$gs->id],
                        ['id'=>$semregDetId])
                    ->execute();
                if ($result) {
                    CCF::checkIfGradeCompleted($stdId,$gset);//pass student Id.
                    echo 1;
                }else{
                    die("<p class='bg-warning'>Updating Error, Please  retry again...!!</p>");
                }
            }else{
                die("<p class='bg-warning'>Semester Registration Entry Error!!</p>");
            }
        }else{
            die("<p class='bg-warning'>Invalid Action with INVALID_USER_TYPE: You are not allowed for this action</p>");
        }
    }
}
