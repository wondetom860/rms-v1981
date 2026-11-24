<?php

namespace frontend\controllers;

use Yii;
use backend\models\ContinuousAssessment2;
use backend\models\ContinuousAssessment2Search;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \backend\models\ContAssessCatagory2;
use \backend\models\InstanceMarkAssignment;
use yii\base\ErrorException;
use \frontend\models\SemesterRegistration2;
use \frontend\models\SemesterRegistration2Detail;
use yii\helpers\Json;
use \common\components\CommonControllerFunctions as CCF;


/**
 * ContinuousAssessment2Controller implements the CRUD actions for ContinuousAssessment2 model.
 */
class ContinuousAssessment2Controller extends Controller
{

    public $acadYearId;
    public $semId;

    protected function __constructor()
    {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }

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
                        'actions' => [],
                        'allow' => true,
                        // Allow  admins to create, update, and delete, and view (index)
                        'roles' => ['@'],
                    ],
                    //    [
                    //        'actions' => [],
                    //        'allow' => false,
                    //        // Allow  admins to create, update, and delete, and view (index)
                    //        'roles' => ['update','delete','view'],
                    //    ],
                ]
            ]
        ];
    }

    public function actionGetInstance()
    {
        $catagoryId = isset($_POST['catagoryId']) && !empty($_POST['catagoryId']) ? intval($_POST['catagoryId']) : die('Invalid Course assessment record, please fix it.');
        $assessmentObject = new InstanceMarkAssignment();
        $insatnces = $assessmentObject->getInstance($catagoryId, 1);

        $options = "<select class='form-control' id='w_t_009_instance' onChange = 'getInstance22($(this).val())'>";
        if ($insatnces) {
            $options .= "<option data-maxPoint={} value=''> - </option>";
            foreach ($insatnces as $key) {
                if (strtoupper($key->ima_instanceName) == 'FINAL EXAM EXAM EXAM') {
                    // check for final exam grade submision date::
                    $actionName = "Final Exam Mark Submission Date";
                    $gset = $key->imaCatagorySetupRel->catCoffRel->coff2detCoff2Rel->coff2GroupRel;
                    $action = \backend\models\AcademicCalanderAction::findOne(['aca_name' => $actionName]);
                    if ($action) {
                        $schedule = CCF::getScheduleForAction($gset, $action->id);
                        if ($schedule) {
                            $options .= "<option data-maxPoint={$key->ima_possiblePoint} value='{$key->id}'>{$key->ima_instanceName}</option>";
                        } else {
                            $options = "Schedule is not open for {$actionName}.";
                        }
                    } else {
                        $options = "Unregistered action [{$actionName}]";
                    }
                } else {
                    $options .= "<option data-maxPoint={$key->ima_possiblePoint} value='{$key->id}'>{$key->ima_instanceName}</option>";
                }
            }
            $options .= "</select>";
            echo $options;
            die();
        } else {
            echo "<p class='bg-danger' style='font-size:8pt; font-style:italic; padding:3px;'>No instance made yet under this catagory. OR your instances are not approved by the course department head.</p>";
            die();
        }
    }

    // a function to return a form to submit a mark for assessment.
    public function actionGetAssessmentSubmissionForm() //
    {
        // $courseId = isset($_POST['courseId']) && !empty($_POST['courseId']) ? $_POST['courseId'] : die('Uknown Course');
        $instId = isset($_POST['instId']) && !empty($_POST['instId']) ? $_POST['instId'] : die('Uknown Course Offering Error.');

        $coff2Det = \backend\models\CourseOffering2Detail::findOne(['id' => $instId]);

        $coffdetId = $instId;
        $courseId = $coff2Det->coff2det_course_relId;
        $catObj = new ContAssessCatagory2();
        $assessmentObject = new InstanceMarkAssignment();

        $course = $coff2Det->coff2detCourseRel->getCourseCredits() . " : " . $coff2Det->coff2detCourseRel->course_title;
        $acy = $coff2Det->coff2detCoff2Rel->coff2GroupRel->gsActiveProgRel->academicYearRel->getAcHeader();
        $section = $coff2Det->coff2detCoff2Rel->coff2GroupRel->getGsDisp() . "(" . $coff2Det->coff2detCoff2Rel->coff2GroupRel->gsGroupRel->group_name . ")";
        $div = "<div style='padding: 10px 30px; border-bottom: 1px solid black; background-color: #FFE;'>
                <h4 style='text-align: center;'>Course Detail</h4>
                <strong>Academic Year:</strong> <u>{$acy}</u><br>
                <strong>Course Info:</strong> <u>{$course}</u><br>
                <strong>Section/Group:</strong> <u>{$section}</u>
                </div>";
        // list assessment catagories for the instructor to choose from.
        $asssessmentCatagory = "<select class='form-control' id='w_t_009' onChange = 'getInstance2({$coffdetId},{$courseId},$(this).val())'>";
        $this->__constructor(); //Initialize global vars.
        $course = \backend\models\Course::findOne(['id' => $courseId]);

        // get assessment catagory setup for the course.
        $catagory = $catObj->getCourseCatagory($courseId, $instId);

        $addMessage = "";
        // check if there is any course add request pending approval
        $cAddRequests = $coff2Det->getCourseAddRequests();
        if ($cAddRequests > 0) {
            $courses = "";
            foreach (
                $coff2Det->getAddRequests()
                    ->andWhere(['ah_approved' => NULL])
                    ->all() as $cAddRequest
            ) {
                $offeringDetiail = $cAddRequest->ahCoffRel;
                $courses .= "[{$cAddRequest->ahStdRel->getFullname()}]: {$offeringDetiail->coff2detCoff2Rel->coff2detAcProgRel->academicYearRel->getAcHeader()} : {$offeringDetiail->coff2detCourseRel->getCourseCredits()} -> {$offeringDetiail->coff2detCourseRel->course_title}";
            }
            $addMessage = "<p class='alert alert-warning' title='{$courses}'><b style='color: red;'>{$cAddRequests}</b> Course Add Request(s) pending approval. Click <a href='/approve-course-add'>here</a> to approve!</p>";
        }
        if ($catagory) {
            $asssessmentCatagory .= "<option value=''>-</option>";
            foreach ($catagory as $key) {
                $catagoryName = \backend\models\CatagoryName2::findOne(['id' => $key->cat_catagory_relId])->catagory_name;
                // get assessment made before under this catagory if any. 
                $assessments = $assessmentObject->getCourseAssessInstances($coffdetId, $key->id);
                // $asInst = "";
                // $rem = NULL;
                // $instance = "<select class='form-control' id='w_t_009_inner' onChange = 'getInstance22({$coffdetId},{$courseId})'>";
                // $maxPoint = $key->cat_maxPoint;
                if ($assessments) {
                    // if assessed before, check if maximum is reached.
                    $assessMentSum = 0;

                    //     foreach ($assessments as $key2) {
                    //         // $assessMentSum += $key2->ma_possiblePoint;
                    //         $instance .= "<option data-maxPoint='{$key2->ima_possiblePoint}' value='{$key2->id}'>{$key2->ima_instanceName}</option>";
                    //     }
                    // $instance .= "</select>";
                } else {
                    continue;
                }
                // $instance = json_encode($instance);
                $asssessmentCatagory .= "<option value='{$key->id}'>{$catagoryName}</option>";
            }
            $asssessmentCatagory .= "</select>";
            return json_encode([1, $addMessage . $asssessmentCatagory, $div]);
            // die();
        } else {
            $addMessage = "<p class='bg-danger'>Assessment Plan NOT known.</p>";
            return json_encode([0, $addMessage, $div]);
        }
    }

    private function calculateTotal2($branchId, $courseId, $semregId, $stdId)
    {
        $totalAssigned = 0;
        $totalgained = 0;
        $grade = 'N/C';
        $jumpedAssessment = false;
        $markedSome = false;
        $ttt = 'Mark Submission NOT COMPLETED yet.';

        $ima = new InstanceMarkAssignment();
        $catObj = new ContAssessCatagory2();
        $contAssObj = new ContinuousAssessment2();

        $instances = $ima->getAssessmentInstances2($courseId);

        $sr2 = SemesterRegistration2Detail::find()
            ->where([
                'srd2_semreg_relId' => $semregId,
                'srd2_course_relId' => $courseId,
            ])
            ->one();
        foreach ($instances as $key2) {
            // For each assessment catagory, get the mark obtained by the student.
            $mark = '-';
            $instanceName = InstanceMarkAssignment::findOne($key2->id);
            $mark = $contAssObj->getStudentMark($courseId, $stdId, $key2->ima_catagorySetup_relId, $instanceName->id);
            if (($instanceName->ima_assessed == 1) && !$mark) { //assessed and but have no valid mark
                $totalAssigned += $instanceName->ima_possiblePoint;
                $jumpedAssessment = true;
            }
            if ($mark) {
                $markedSome = true;
                $totalAssigned += $instanceName->ima_possiblePoint;
                $markDtae = $mark->ca_created_at;
                $mark = $mark->ca_markObtained;
                $totalgained += $mark;
                // $asessDate = date_format(date_create($markDtae), 'D, d M Y');
                // $table .= "<td style='text-align:center;' title='{$asessDate}'>" . $mark . "</td>";
            }
        }
        $gradeObj = $contAssObj->setLetterGrade($totalAssigned, $totalgained, $stdId, NULL, $sr2->id, $jumpedAssessment, $markedSome);
        $ttt = 'Letter Grade equivalent.';
        // if ($gradeObj ) {
        //     $grade = $gradeObj->gs_ltr_grade;
        // }
    }

    public function actionGetMarkSheetDetail()
    { //actionGetMarkSheetDetail
        $params = isset($_GET['params']) && !empty($_GET['params']) ? (string)$_GET['params'] : die('Invalid Request');
        $params = json_decode($params);
        $this->layout = 'printLayout';
        $_SESSION['report'] = null;
        return $this->render('__report/marksheet-report-2', ['coffdetId' => $params[0]]);
    }

    public function actionGetGradeReportDetail()
    { //get-grade-report-detail
        $params = isset($_GET['params']) && !empty($_GET['params']) ? (string)$_GET['params'] : die('Invalid Request');
        $params = json_decode($params);
        $this->layout = 'printLayout';
        $_SESSION['report'] = null;
        return $this->render('__report/grade-report', ['coffdetId' => $params[0], 'print' => false]);
    }

    private function drawMarkSheetTable($coffdetId, $print = false)
    {
        $table = '';
        $contAssObj = new ContinuousAssessment2();
        $ima = new InstanceMarkAssignment();

        $coff2DetId = \backend\models\CourseOffering2Detail::findOne(['id' => $coffdetId]);

        $courseId = $coff2DetId->coff2det_course_relId;
        $acadYearId = $coff2DetId->coff2detCoff2Rel->coff2_acadYear_relId;
        $semId = $coff2DetId->coff2detCoff2Rel->coff2_sem_relId;

        if ($coff2DetId) {
            $gsetId = \backend\models\CourseOffering2::findOne(['id' => $coff2DetId->coff2det_coff2_relId])->coff2_group_relId;
        } else {
            return "<p class='bg-warning'>Invalid Orrefing record.</p>";
            // die();
        }


        $srd = \frontend\models\SemesterRegistration2Detail::find()
            ->where(['srd2_course_relId' => $courseId, 'srd2_group_relId' => $gsetId,])
            ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
            ->innerJoin('student', 'student.id = semester_registration_2.semreg_std_relId')
            ->innerJoin('user', 'user.id = student.std_user_relId')
            ->andWhere([
                'semreg_acadYear_relId' => $acadYearId,
                'semreg_semester_relId' => $semId,
            ])
            ->with('srd2SemregRel')
            ->orderBy('user.first_name')
            ->all();


        $gset = \backend\models\WGroupSetup::findOne(['id' => $gsetId]);
        // Get inst groups from course offering detail.

        // $dept = \backend\models\Department::findOne(['id' => $gset->gs_dept_relId])->dept_name;
        // $clslvl = \backend\models\ClassLevel::findOne(['id' => $gset->gs_clslvl_relId])->clslvl_num;
        $instNmae = $coff2DetId->coff2detInstRel->getFullName();
        // $gname = \backend\models\WGroup::findOne(['id' => $gset->gs_group_relId])->group_name;
        $course = $coff2DetId->coff2detCourseRel;

        $acadyear = $gset->gsAcadYearRel->sch_year_desc . '-' . $gset->gsSemRel->semester_name;

        if ($srd) {

            $btnPrint = null;
            $headerText = "";
            if (!$print) {
                $headerText = "<h2>" . strtoupper(Yii::$app->params['client']) . ', ' . Yii::$app->params['college'] . "</h2>";
            }

            $table .= "<table class='table table-hover table-sm table-bordered table-condensed' style='font-size:9pt; border-color:#000;'>";
            $table .= "<caption style='text-align:center; font-size:10pt;'>
                    {$headerText}
                    <h3 style='text-decoration:underline; text-align:center;'>Mark Sheet Report</h3>
                    Department:<u><i>{$gset->gsDeptRel->dept_name}</i></u>&nbsp;&nbsp;&nbsp;<br>
                    Course Title:<u><i>{$course->course_title}</i></u>&nbsp;&nbsp;&nbsp;<br>
                    Class Level:<u><i>{$gset->gsClslvlRel->clslvl_num}</i></u>&nbsp;&nbsp;&nbsp;
                    Program:<u><i>{$gset->gsAdmRel->admission_name} {$gset->gsProgRel->program_name}</i></u>&nbsp;&nbsp;&nbsp;
                    Group:<u><i>{$gset->gsGroupRel->group_name}</i></u>&nbsp;&nbsp;&nbsp;<br>
                    Instructor Name : <u><i>{$instNmae}</i></u>&nbsp;&nbsp;&nbsp;
                    Academic Year : <u><i>{$acadyear}</i></u>
                </caption>";
            // $mdCatagory = $catObj->getCourseCatagor($course->id,);

            // $instances = $contAssObj->getAssessmentInstances($course->id, $coffdetId);
            $instances = $ima->getAssessmentInstances2($course->id, NULL, $acadYearId, $semId);
            $table .= "<thead style='background-color:#ace;border-color:#000;'>
                <th style='border-color:#000;'>#</th>
                <th colspan=2 style='text-align:center; border-color:black;'>Student Detail</th>
                <th colspan=1 style='text-align:center; border-color:black;'>Crs Added</th>
                <th colspan=" . (count($instances) + 2) . " style='text-align:center; border-color:#000;'>Assessment detail</th>
                
                
                </thead>";
            $table .= "<thead style='background-color:#ace; border-color:black;'>
                    <th style='border-color:#000;'>#</th>
                    <th style='border-color:#000;'>Reg. ID.</th>
                    <th style='border-color:#000;'>Student Name</th>
                    <th style='border-color:#000;'>Crs. Added</th>
                    ";
            // get assessment catagries (sub_catagories if any) made for the course and append them as the headr.

            if ($instances) {
                $namesArray = [];

                foreach ($instances as $key) {
                    // display actual instances of assessment under the category.
                    // $catagoryName = \backend\models\ContAssessCatagoryName::findOne(['id'=>$key->cat_name_relId])->cont_cat_name;
                    $instanceName = InstanceMarkAssignment::findOne($key->id);
                    if (!in_array($instanceName->ima_instanceName, $namesArray)) {
                        $namesArray[] = $instanceName->ima_instanceName;
                        // check if is course Instructor::
                        $ppp = '';
                        if (Yii::$app->user->can('Instructor') || Yii::$app->user->can('DepartmentHead')) {
                            $inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);

                            if (!is_null($coff2DetId->coff2det_inst_relId) && $coff2DetId->coff2det_inst_relId == $inst->id) {
                                $ppp = "<p class='btn btn-link btn-xs' onclick='LoadModalForEdit({$key->id},{$gsetId},{$course->id})' title='Click here to change student mark entered under this instance.'>" . $instanceName->ima_instanceName . "(" . $instanceName->ima_possiblePoint . "%)</p>";
                            } else {
                                $ppp = $instanceName->ima_instanceName . "(" . $instanceName->ima_possiblePoint . "%)";
                            }
                        } else {
                            $ppp = $instanceName->ima_instanceName . "(" . $instanceName->ima_possiblePoint . "%)";
                        }
                        $table .= "<th style='text-align:center; border-color:#000;'>{$ppp}</th>";
                    }
                }
            } else {

                $table .= "<th colspan=" . (count($instances) - 1) . "><p class='bg-danger'>No mark submitted for this course yet.</p></th>";
            }

            $table .= "<th style='text-align:center;border-color:#000;'>Total.</th>
                    <th style='border-color:#000;'>Grade</th>";

            // get assessments made under this category.
            // echo "<thead style='background-color:#bbe;'>
            //             <th></th>
            //             <th></th>
            //             <th></th>
            //             <th></th>
            //             ";
            $table .= "</thead><tbody>";
            $count = 0;
            foreach ($srd as $std) {

                $s = \backend\models\Student::findOne(['id' => $std['srd2SemregRel']['semreg_std_relId']]);
                $u = \backend\models\User::findOne(['id' => $s->std_user_relId]);

                $gid = $std->srd2_grade_relId;
                //$grade = is_null($gid)?'-':GradeScale::findOne(['id'=>$gid])->gs_ltr_grade;
                $caddda = is_null($std->srd2_coadd_flag) ? 'NO' : 'YES';
                $table .= "<tr>
                      <td style='border-color:#000;'>" . ++$count . "</td>
                      <td style='border-color:#000;'>" . $s->std_regId . "</td>
                      <td style='border-color:#000;'>" . $u->first_name . ' ' . $u->middle_name . "</td>
                      <td style='border-color:#000;'>" . $caddda . "</td>
                     ";
                $totalAssigned = 0;
                $totalgained = 0;
                $grade = 'N/C';
                $jumpedAssessment = false;
                $ttt = 'Mark Submission NOT COMPLETED yet.';
                foreach ($instances as $key2) {
                    // For each assessment catagory, get the mark obtained by the student.
                    $mark = '-';
                    $instanceName = InstanceMarkAssignment::findOne($key2->id);
                    $mark = $contAssObj->getStudentMark($courseId, $s->id, $key2->ima_catagorySetup_relId, $instanceName->id);
                    if (($instanceName->ima_assessed == 1)) { //assessed and but have no valid mark
                        if ($mark) {
                            $markDtae = $mark->ca_created_at;
                            $mark = $mark->ca_markObtained;
                            $totalgained += $mark;
                            $asessDate = date_format(date_create($markDtae), 'D, d M Y');
                            $table .= "<td style='text-align:center; border-color:#000;' title='{$asessDate}'>" . $mark . "</td>";
                        } else {
                            $jumpedAssessment = true;
                            $table .= "<td style='text-align:center; border-color:#000; background-color:yellow;' title='Missed'>-</td>";
                        }
                        $totalAssigned += $instanceName->ima_possiblePoint;
                    } else {
                        $table .= "<td style='text-align:center; border-color:#000; background-color:red;' title='Not Assessed yet.'>-</td>";
                    }
                }
                // $icon = '';
                $table .= "<td style='text-align:right; font-weight:bold; border-color:#000; background-color:#dff'>{$totalgained}/{$totalAssigned}</td>";

                if ($totalAssigned >= 100) {
                    // Indicates the mark submission is completed. Ready to calculate LetterGrade depending on the grade scale.
                    $gradeObj = $contAssObj->setLetterGrade($totalAssigned, $totalgained, $std['srd2SemregRel']['semreg_std_relId'], $coffdetId, $std['id'], $jumpedAssessment);

                    if ($gradeObj) {
                        $grade = $gradeObj->gs_ltr_grade;
                    } else {
                        $grade = "-";
                    }
                    // add print option if the grade is calculated
                    if (is_null($btnPrint) && $print) {
                        $btnPrint = "<button class='btn btn-xs btn-warning pull-right' title='Print this mark sheet' onclick='markSheet({$coffdetId});return false;'>Print</button>";
                    }
                }
                $table .= "<td style='text-align:center; background-color:#cff;border-color:#000;' title='{$ttt}'>{$grade}</td>";
                $table .= "</tr>";
            }
            $table .= "</tbody></table>";
            $count = $count == 0 ? 'NO Result ' : ($count == 1 ? $count . ' Result ' : $count . ' Results ');
            $table .= "<p class='bg-success'><i><b>" . $count . "</b></i>.{$btnPrint}</p>";
            return $table;
            // die();
        } else {
            return "<p class='bg-warning'>No student enrolled this course in this semester.</p>";
        }
    }

    public function actionEnrstudlists()
    {
        $instId = isset($_POST['instId']) && !empty($_POST['instId']) ? $_POST['instId'] : die('Uknown Course course Error.');
        $coffdetId = intval($_POST['instId']);
        if (isset($_POST['foredit']) && !empty($_POST['foredit'])) {
            return $this->renderPartial('__report/marksheet-report', ['coffdetId' => $coffdetId, 'print' => true]);
        }
        return $this->renderPartial('__report/marksheet-report-2', ['coffdetId' => $coffdetId, 'print' => true]);
    }

    public function actionProgressReport()
    {
        if (Yii::$app->user->can('DeparymentHead')) {
            return $this->render('progressReport');
        }
    }
    public function actionEnrstudlists2()
    {
        if (1 == 1) {

            // $courseId = isset($_POST['courseId']) && !empty($_POST['courseId']) ? $_POST['courseId'] : die('Uknown Course');
            $instId = isset($_POST['instId']) && !empty($_POST['instId']) ? $_POST['instId'] : die('Uknown Course course Error.');
            $this->__constructor(); //Initialize global vars.

            // catagoryId
            // instanceId
            $coffdetId = $_POST['instId'];
            $courseId = $_POST['courseId'];
            $maxPoint = $_POST['maxPoint'];
            $catagoryId = $_POST['catagoryId'];
            $instanceId = $_POST['instanceId'];
            $catObj = new ContAssessCatagory2();
            $contAssObj = new ContinuousAssessment2();

            $coff2DetId = \backend\models\CourseOffering2Detail::findOne(['id' => $coffdetId]);
            if ($coff2DetId) {
                $courseId = $coff2DetId->coff2det_course_relId;
                $gsetId = \backend\models\CourseOffering2::findOne(['id' => $coff2DetId->coff2det_coff2_relId])->coff2_group_relId;
            } else {
                echo "<p class='bg-warning'>Invalid Orrefing record.</p>";
                die();
            }

            // if ima_name is FINAL EXAM , check for its submision date.
            $ima = \backend\models\InstanceMarkAssignment::findOne($instanceId);
            if ($ima) {
                if (str_contains(strtoupper($ima->ima_instanceName),'FINAL EXAM') || (strtoupper($ima->imaCatagorySetupRel->catCatagoryRel->catagory_name) == 'FINAL EXAM')) {
                    // check for final exam mark submission date schedule.
                    $actionName = "Final Exam Mark Submission Date";
                    $gset = \backend\models\WGroupSetup::findOne($gsetId);
                    $action = \backend\models\AcademicCalanderAction::findOne(['aca_name' => $actionName]);
                    if ($action) {
                        $schedule = CCF::getScheduleForAction($gset, $action->id);
                        if ($schedule) {
                            // $options .= "<option data-maxPoint={$key->ima_possiblePoint} value='{$key->id}'>{$key->ima_instanceName}</option>";
                        } else {
                            return "Schedule is not open for {$actionName}.";
                        }
                    } else {
                        return "Unregistered action [{$actionName}] for {$ima->ima_instanceName}";
                    }
                }
            }

            $srd = \frontend\models\SemesterRegistration2Detail::find()
                ->where(['srd2_course_relId' => $courseId, 'srd2_group_relId' => $gsetId,])
                ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                ->innerJoin('student', 'student.id = semester_registration_2.semreg_std_relId')
                ->innerJoin('user', 'user.id = student.std_user_relId')
                ->andWhere([
                    'semreg_acadYear_relId' => $coff2DetId->coff2detCoff2Rel->coff2_acadYear_relId,
                    'semreg_semester_relId' => $coff2DetId->coff2detCoff2Rel->coff2_sem_relId,
                ])
                ->with('srd2SemregRel')
                ->orderBy('user.first_name')
                ->all();


            $gset = \backend\models\WGroupSetup::findOne(['id' => $gsetId]);
            // Get inst groups from course offering detail.

            $dept = $gset->gsDeptRel->dept_name;
            $clslvl = $gset->gsClslvlRel->clslvl_num;
            $gname = $gset->gsGroupRel->group_name;
            $course = \backend\models\Course::findOne(['id' => $courseId]);

            $wt_rep = "";

            if ($srd) {

                $wt_rep .= "<table class='table table-hover table-sm table-bordered table-condensed' style='font-size:9pt;'>";
                $wt_rep .= "<caption>
                    <h4 style='text-decoration:underline; text-align:center;'>Mark Sheet.</h4>
                    Department:<u><i>{$dept}</i></u>&nbsp;&nbsp;&nbsp;<br>
                    Class Level:<u><i>{$clslvl}</i></u><br>
                    Group:<u><i>{$gname}</i></u>&nbsp;&nbsp;&nbsp;<br>
                    Course:<u><i>{$course->course_title}</i></u>&nbsp;&nbsp;&nbsp;<br>
                    </caption>";
                $wt_rep .= "<thead style='background-color:#ace;'>
                        <th>#</th>
                        <th>Reg. ID.</th>
                        <th>Student Name</th>
                        <th>Mark ({$maxPoint})%</th>
                        ";

                $wt_rep .= "</thead><tbody>";
                $count = 0;
                foreach ($srd as $std) {
                    // check if already assessed this instance before displaying...
                    // if ($this->assessed($std['srd2SemregRel']['semreg_std_relId'],$courseId,$catagoryId,$instanceId,$std['srd2SemregRel']['id'])) {
                    //     continue;
                    // }
                    $exists = ContinuousAssessment2::find()
                        ->where([
                            'ca_semreg_relId' => $std['srd2SemregRel']['id'],
                            'ca_markAssignment_relId' => $instanceId,
                            'ca_std_relid' => $std['srd2SemregRel']['semreg_std_relId'],
                        ])
                        ->andWhere(['IS NOT', 'ca_markObtained', NULL])
                        ->One();
                    if ($exists) {
                        continue;
                    }
                    $s = \backend\models\Student::findOne(['id' => $std['srd2SemregRel']['semreg_std_relId']]);
                    $u = \backend\models\User::findOne(['id' => $s->std_user_relId]);
                    $caddda = "<input data-stdId = '{$s->id}' class='__markInput' type='Number' step='0.01' max='{$maxPoint}' min='0' value='' style='text-align:right;' />";
                    $wt_rep .= "<tr>
                          <td>" . ++$count . "</td>
                          <td>" . $s->std_regId . "</td>
                          <td>" . $u->first_name . ' ' . $u->middle_name . "</td>
                          <td style='text-align:center;'>" . $caddda . "</td>
                         ";

                    $wt_rep .= "</tr>";
                }

                $wt_rep .= "</tbody></table>";
                // $count = $count == 0 ? 'NO Student ' : ($count == 1 ? $count . ' Student ' : $count . ' Students ');
                if ($count > 0) {
                    $wt_rep .= "<span style='font-style:italic; float:left;'>{$count} record found.</span><br><p class='bg-success'><button class='btn btn-xs btn-success' onclick='dhfgfvbsfgshgfjsdfatydasdv()' style='float:right;'>Save</button></p>";
                } else {
                    $wt_rep .= "<span style='font-style:italic; float:right; color:red;'>{$count} record found.</span><br>";
                }
                return $wt_rep;
            } else {
                echo "<p class='bg-warning'>No student enrolled this course in this semester.</p>";
            }
        } else {
            echo "<p>No data found.</p>";
            die();
        }
    }

    // private function assessed($stdId,$catagoryId,$instanceId,$semregId){
    //     return \backend\models\ContinuousAssessment2::findOne(['ca_std_relid'=>$stdId,'ca_catagorySetup_relId'=>$catagoryId,'ca_semreg_relId'=>$semregId,'ca_markAssignment_relId'=>$instanceId]) > 0;
    // }
    /**
     * Lists all ContinuousAssessment2 models.
     * @return mixed 
     */
    public function actionIndex()
    {
        $searchModel = new ContinuousAssessment2Search();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        $model = new ContinuousAssessment2();


        if (Yii::$app->request->post('hasEditable')) {
            $branchId = Yii::$app->request->post('editableKey');
            $branch = ContinuousAssessment2::findOne($branchId);

            $post = [];
            // var_dump($_POST);die();
            // ContinuousAssessment2[0][ca_markObtained]
            $post['ContinuousAssessment2'] = current($_POST['ContinuousAssessment2']);
            $output = Json::encode(['output' => '', 'message' => '']);
            if ($branch->load($post)) {

                $ima = InstanceMarkAssignment::findOne($branch->ca_markAssignment_relId);
                if ($ima->ima_possiblePoint < $branch->ca_markObtained) {
                    $output = Json::encode(['output' => '0', 'message' => 'Mark should NOT be more than ' . $ima->ima_possiblePoint]);
                } else {
                    // check if the grade for the course is approved.
                    $courseId = \backend\models\ContAssessCatagory2::findOne($ima->ima_catagorySetup_relId)->cat_course_relId;

                    $approved = \frontend\models\SemesterRegistration2Detail::find()
                        ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                        ->where([
                            'semester_registration_2.id' => $branch->ca_semreg_relId,
                        ])
                        ->andWhere([
                            'semester_registration_2.semreg_std_relId' => $branch->ca_std_relid,
                            'srd2_course_relId' => $courseId,
                        ])
                        ->one();

                    if ($approved) {
                        if ($approved->srd2_gradeApproved == 1) {
                            $output = Json::encode(['output' => '0', 'message' => 'You can NOT change approved grade mark. ']);
                        } else {
                            $branch->save();

                            // $_POST['courseId'] = $courseId;
                            // $_POST['instId'] = $branch->ca_coff_relId;
                            $message = $this->calculateTotal2($branch->id, $courseId, $branch->ca_semreg_relId, $branch->ca_std_relid);
                            // return Json::encode(['message' => $message]);
                            // $output = 
                        }
                    } else {
                        $output = Json::encode(['output' => '0', 'message' => 'Semester registration Error for the student. ']);
                    }
                }
            }
            echo $output;
            $branch = NULL;
            return;
        }

        if (Yii::$app->request->isAjax) {
            if (isset($_POST['instanceId']) && !empty($_POST['instanceId'])) {
                // filter thos assessment taken under selecetd instance, for selected group.

            }
            return $this->renderAjax('indexAjax', [
                'searchModel' => $searchModel,
                'dataProvider' => $dataProvider,
                'model' => $model,
            ]);
        }

        return $this->render('index', [
            'searchModel' => $searchModel,
            'dataProvider' => $dataProvider,
        ]);


        // if request type is ajax, render indexAjax for view.
    }

    /**
     * Displays a single ContinuousAssessment2 model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    // myCreate function
    // save the mark list submitted by the instructor.

    public function actionCreate2()
    {
        $coffdetId = isset($_POST['coffdetId']) && !empty($_POST['coffdetId']) ? intval($_POST['coffdetId']) : die('Invalid Mark submission. Please correct and re-try');
        // $courseId = isset($_POST['courseId']) && !empty($_POST['courseId']) ? intval($_POST['courseId']) : die('Invalid Mark submission. Please correct and re-try');
        $maxPoint = isset($_POST['maxPoint']) && !empty($_POST['maxPoint']) ? $_POST['maxPoint'] : die('Max. point not set for the assessment, please set one.');
        $catagory = isset($_POST['catagory']) && !empty($_POST['catagory']) ? intval($_POST['catagory']) : die('Assessment catagory selection error. Pleasse correct.');

        $studentList = isset($_POST['studentList']) && !empty($_POST['studentList']) ? $_POST['studentList'] : die('Student List empity, please mark atleast one student.');
        $instanceName = isset($_POST['instanceName']) && !empty($_POST['instanceName']) ? $_POST['instanceName'] : die('Instance Name error, Please retry.');

        // coffdetId
        // courseId
        // maxPoint
        // instanceName
        // studentList
        // catagory
        $coff2DetId = \backend\models\CourseOffering2Detail::findOne(['id' => $coffdetId]);
        $courseId = $coff2DetId->coff2det_course_relId;
        $transaction = Yii::$app->db->beginTransaction();
        try {
            // create instance of assessment,
            $studentList = json_decode($studentList);
            if ($studentList) {
            } else {
                throw new ErrorException("Student List empty, Please select one.", 1);
            }

            $message = [];
            // $assessmentObject = new ContAssessMarkAssignment();
            // $assessmentObject->ma_possiblePoint = $maxPoint;
            // $assessmentObject->ma_catagory_relId = $catagory;
            // $assessmentObject->ma_instance = $instanceName;
            // $assessmentObject->cat_coff_relId = $coffdetId;

            // $assessmentObject->ma_created_by = $assessmentObject->ma_updated_by = Yii::$app->user->identity->id;
            // $assessmentObject->ma_created_at = $assessmentObject->ma_updated_at = date('Y-m-d h:i:s');

            // if ($assessmentObject->save()) {
            foreach ($studentList as $key => $value) {

                // get Semreg detail of the student for this semester.
                $semregObj = new SemesterRegistration2();
                $model = new ContinuousAssessment2();

                $ss = $model->getCaSemregRel2($key, $courseId, $coff2DetId);
                if ($ss) {
                    $model->ca_semreg_relId = $ss->id;
                } else {
                    $message = [
                        'std' => $key,
                        'message' => "Student not enrolled this course, please check if any is error."
                    ];
                    continue;
                }

                // check if made before

                $model->ca_markAssignment_relId = $instanceName;
                $model->ca_coff_relId = $coffdetId;
                $model->ca_std_relid = $key;
                $model->ca_markObtained = $value;
                $model->ca_catagorySetup_relId = $catagory;

                $model->ca_created_by = $model->ca_updated_by = Yii::$app->user->identity->id;
                $model->ca_created_at = $model->ca_updated_at = date('Y-m-d h:i:s');

                // WONDE_APPROVED_GRADE_UPDATE_ERROR!!

                // check if the grade for the course is approved.
                $ima = \backend\models\InstanceMarkAssignment::findOne($instanceName);
                $courseId = \backend\models\ContAssessCatagory2::findOne($ima->ima_catagorySetup_relId)->cat_course_relId;

                $approved = \frontend\models\SemesterRegistration2Detail::find()
                    ->innerJoin('semester_registration_2', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
                    ->where([
                        'semester_registration_2.id' => $model->ca_semreg_relId,
                    ])
                    ->andWhere([
                        'semester_registration_2.semreg_std_relId' => $model->ca_std_relid,
                        'srd2_course_relId' => $courseId,
                    ])
                    ->one();

                if ($approved) {
                    if ($approved->srd2_gradeApproved == 1) {
                        $output = Json::encode(['output' => '0', 'message' => 'You can NOT change approved grade mark. ']);
                        continue;
                    }
                }

                // check if exists!
                // 'ca_coff_relId'=>$model->ca_coff_relId
                $exists = ContinuousAssessment2::find()
                    ->where([
                        'ca_markAssignment_relId' => $model->ca_markAssignment_relId,
                        'ca_std_relid' => $model->ca_std_relid,
                        'ca_catagorySetup_relId' => $model->ca_catagorySetup_relId,
                    ])
                    ->one();
                if ($exists) {
                    $result = Yii::$app->db->createCommand()
                        ->update(
                            'continuous_assessment2',
                            [
                                'ca_markObtained' => $model->ca_markObtained,
                                'ca_created_by' => Yii::$app->user->identity->id,
                                'ca_created_at' => date('Y-m-d h:i:s')
                            ],
                            [
                                'id' => $exists->id
                            ]
                        )
                        ->execute();
                    if ($result) {
                        continue;
                    }
                }
                // similar doesnit exists, save the new record.
                if ($model->save()) {
                    $message = [
                        'std' => $key,
                        'message' => "successful."
                    ];
                    continue;
                } else {
                    $message = [
                        'std' => $key,
                        'message' => $model->errors,
                    ];
                }
            }
            // update the instance is assess to true | 1.
            $result = Yii::$app->db->createCommand()
                ->update('instance_mark_assignment', ['ima_assessed' => 1], ['id' => $instanceName])
                ->execute();
            $transaction->commit();
            echo 1;
            return;
            // } else {
            //     var_dump($assessmentObject->errors);
            //     die();
            //     //throw new ErrorException(, 1);
            // }

            // save mark for each student under this assessment.
        } catch (ErrorException $e) {
            $transaction->rollBack();
            var_dump($e->getMessage());
            die();
        }
    }

    /**
     * Creates a new ContinuousAssessment2 model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new ContinuousAssessment2();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing ContinuousAssessment2 model.
     * If update is successful, the browser will be redirected to the 'view' page.
     * @param integer $id
     * @return mixed
     */
    public function actionUpdate($id)
    {
        $model = $this->findModel($id);

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('update', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Deletes an existing ContinuousAssessment2 model.
     * If deletion is successful, the browser will be redirected to the 'index' page.
     * @param integer $id
     * @return mixed
     */
    public function actionDelete($id)
    {
        $this->findModel($id)->delete();

        return $this->redirect(['index']);
    }

    /**
     * Finds the ContinuousAssessment2 model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return ContinuousAssessment2 the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = ContinuousAssessment2::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
