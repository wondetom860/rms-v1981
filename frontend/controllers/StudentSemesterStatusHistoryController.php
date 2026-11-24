<?php

namespace frontend\controllers;

use Yii;
use backend\models\StudentSemesterStatusHistory;
use backend\models\StudentSemesterStatusHistorySearch;
use backend\models\WGroupSetup;
use frontend\models\SemesterRegistration2Detail;
use yii\web\Controller;
use yii\web\NotFoundHttpException;
use yii\filters\VerbFilter;
use \common\components\CommonControllerFunctions as CCF;
use Exception;
use yii\base\ErrorException;

/**
 * StudentSemesterStatusHistoryController implements the CRUD actions for StudentSemesterStatusHistory model.
 */
class StudentSemesterStatusHistoryController extends Controller
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
        ];
    }

    /**
     * Lists all StudentSemesterStatusHistory models.
     * @return mixed
     */
    public function actionIndex()
    {
        $searchModel = new StudentSemesterStatusHistorySearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);

        $model = new StudentSemesterStatusHistory();
        return $this->render('index', [
            'model' => $model,
        ]);
    }

    public function actionGetSemesterOutstandingStudents()
    {
        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die('Select  Academic year..');
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('Select  Semester..');
        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Select  Admission');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Select  Program');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Select  Class Year.');

        // $this->__constructor();

        $semStatus = 'Honor List';

        $statuEntry = \backend\models\HonorListControl::find()->one();

        $max = 0;
        $min = 0;
        if ($statuEntry) {
            $max = $statuEntry->hl_maxSGPA;
            $min = $statuEntry->hl_minSGPA;
        } else {
            echo "<p class='bg-warning'>No such status entry found. Please configure one named '{$semStatus}'</p>";
            die();
        }
        // ->andWhere(['semreg_SGPA '])
        // ->innerJoin('semester_registration_2_detail', 'semester_registration_2.id = semester_registration_2_detail.srd2_semreg_relId')
        $semreg = \backend\models\SemesterRegistration2::find()
            ->innerJoin('w_group_assignment', 'semester_registration_2.semreg_std_relId = w_group_assignment.gas_std_relId')
            ->innerJoin('w_group_setup', 'w_group_setup.id = w_group_assignment.gas_gset_relId')
            ->where([
                'semreg_acadYear_relId' => $acadYearId,
                'semreg_semester_relId' => $semId,
                'gs_acadYear_relId' => $acadYearId,
                'gs_sem_relId' => $semId,
            ])
            ->andWhere(['IS NOT', 'semreg_SGPA', NULL])
            ->andWhere([
                'gs_adm_relId' => $admId,
                'gs_prog_relId' => $progId,
                'gs_clslvl_relId' => $clslvlId,
            ])
            ->andWhere('semreg_SGPA between :min AND :max', [':min' => $min, ':max' => $max])
            ->andWhere(['semreg_gradeCompleted' => 1])
            ->orderBy(['semreg_SGPA' => SORT_DESC])
            ->all();

        if ($semreg) {

            $wonderep  = "";
            $acadYear = \backend\models\SchoolYear::findOne($acadYearId)->sch_year_desc;
            $sem = \backend\models\Semester::findOne($semId)->semester_name;

            $adm = \backend\models\Admission::findOne($admId)->admission_name;
            $prog = \backend\models\Program::findOne($progId)->program_name;
            $clslvl = \backend\models\ClassLevel::findOne($clslvlId);

            $batch = $clslvl->getClslvlDisp();


            $wonderep .= "<div class='container bg-info' style='text-align:center; border-bottom:1px solid black'>
                    <p style='font-weight:bold; text-decoration:underline'>List of Out Standing Students</p>
                    <p> Semester <b>{$sem}</b> of Academic Year <b>{$acadYear}</b></p>
                    <p> Program <b> {$adm} {$prog}</b> <b>{$batch}</b> Year.</p>
                    </div>";

            $deptsIndex = [];
            $gAssModel = new \backend\models\WGroupAssignment();
            foreach ($semreg as $keySemreg) {
                $gas = $gAssModel->getStudentGroupAssignment($keySemreg->semregAcadYearRel, $keySemreg->semregSemesterRel, $keySemreg->semregStdRel);
                if ($gas) {
                    $deptsIndex[$gas->gasGsetRel->gs_dept_relId][] = [$keySemreg->id, $keySemreg->semreg_SGPA];
                }
            }

            foreach ($deptsIndex as $deptId => $stds) {
                if (empty($stds)) {
                    continue;
                }
                $dept = \backend\models\Department::findOne($deptId);
                $DD = strtoupper($dept->dept_name);
                $wonderep .= "
                    <div class='panel panel-default'>
                        <div class='panel-heading'>
                            DEPARTMENT OF {$DD}
                        </div>
                        <div class='panel-body'>
                            <div class='row'>
                            ";
                foreach ($stds as $std) {
                    $std = $std[0];
                    $std = \backend\models\SemesterRegistration2::findOne($std);
                    // $fname = $st->srd2SemregRel->semregStdRel->getFullName();
                    $user = $std->semregStdRel->stdUserRel;
                    $sgpa = sprintf("%0.2f", round($std->semreg_SGPA, 2));
                    //  style='text-align:center;' 
                    $fname = strtoupper($user->getFullName());
                    $wonderep .= "<div class='col-md-4'><center><br>
                            <img class='img img-circle' src='/uploads/" . $user->photo_num . "' width='120' height='140' />
                            <p class='bg-default' style='font-weight:bold;font-size:9pt;'>{$fname}<br>&nbsp;&nbsp;&nbsp; SGPA = <u>{$sgpa}</u> </p>
                            </center>
                        </div> ";
                }
                $wonderep .= "
                            </div>
                        </div>
                    </div>
                    ";
            }
            return $wonderep;
        } else {

            echo "<p class='bg-warning'>No record found.</p>";
            die();
        }
    }

    public function actionGetStudentSemesterStatus()
    {
        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die('acadYearId Error..');
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('semId Error..');
        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Uknown Admission');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Uknown Program');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Uknown Class Year.');

        $gs = new WGroupSetup();
        $gsets = $gs->getGsets($admId, $progId, NULL, $clslvlId, $acadYearId, $semId);
        // getGsets($admId,$progId,$deptId,$clslvlId,$acadYear,$semId)

        $ext = false;
        if ($gsets) {
            $clslvlObj = \backend\models\ClassLevel::findOne($clslvlId);
            $semObj = \backend\models\Semester::findOne($semId);
            $ayObj = \backend\models\SchoolYear::findOne($acadYearId);
            $progObj = \backend\models\Program::findOne($progId);
            $admObj = \backend\models\Admission::findOne($admId);

            $ext = $admObj->admission_name == 'Extension' ? true : false;

            $colCount = $clslvlObj->clslvl_num * 3 * ($ext ? 3 : 2);
            $cl = $clslvlObj->clslvl_num == 1 ? "1<sup>st</sup>" : ($clslvlObj->clslvl_num == 2 ? "2<sup>nd</sup>" : ($clslvlObj->clslvl_num == 3 ? "3<sup>rd</sup>" : $clslvlObj->clslvl_num . "<sup>th</sup>"));

            echo "<table class='table table-striped table-hover table-condensed table-sm' style='font-size:9pt;'>";
            echo "<caption style='font-weight: normal; color:black;'>
                <center>
                    " . strtoupper(Yii::$app->params['client']) . ", " . strtoupper(Yii::$app->params['college']) . "<br>
                    OFFICE OF THE REGISTRAR<br>
                    <b><u>Student Semester Status History</u></b><br>

                    {$semObj->semester_name} Semester of Academic Year {$ayObj->sch_year_desc}<br>

                    Program : <u>{$admObj->admission_name} {$progObj->program_name} {$cl} Year</u>
                </center>
            </caption>";
            echo "<thead style='background-color: #8D0;'>";
            echo "<th rowspan = 4 style='border:1px solid black; vertical-align:middle;'>#</th>
                <th rowspan = 4 style='border:1px solid black; vertical-align:middle; text-align:left;'>Reg. NO.</th>
                <th rowspan = 4 style='border:1px solid black; vertical-align:middle; text-align:left;'>Full Name</th>
                <th colspan = {$colCount} style='border:1px solid black; text-align:center;'>Semester Grade History</th>
                </thead>";
            // Class Year row
            echo "<thead  style='background-color: #8D0;'>
                    <th style='border:1px solid black;'></th>
                    <th style='border:1px solid black;'></th>
                    <th style='border:1px solid black;'></th>";
            $cl = 1;
            $cd = $colCount / $clslvlObj->clslvl_num;
            while ($cl <= $clslvlObj->clslvl_num) {
                echo "<th colspan={$cd} style='text-align:center; border:1px solid black;'>Year - {$cl}</th>";
                $cl++;
            }
            echo "</thead>";
            // Semesters Row
            echo "<thead style='background-color: #8D0;'>
                    <th style='border:1px solid black;'></th>
                    <th style='border:1px solid black;'></th>
                    <th style='border:1px solid black;'></th>";
            $cl = 1;
            while ($cl <= $clslvlObj->clslvl_num) {

                echo "<th colspan=3 style='border:1px solid black; text-align:center;'>{$cl}-I</th>
                      <th colspan=3 style='border:1px solid black; text-align:center;'>{$cl}-II</th>";
                if ($ext) {
                    echo "<th colspan=3 style='border:1px solid black; text-align:center;'>{$cl}-III</th>";
                }
                $cl++;
            }
            echo "</thead>";
            // GPA row
            echo "<thead style='background-color: #8D0;'>
                    <th style='border:1px solid black;'></th>
                    <th style='border:1px solid black;'></th>
                    <th style='border:1px solid black;'></th>";
            $cl = 1;
            while ($cl <= $clslvlObj->clslvl_num) {

                // Sem - I
                echo "<th style='border:1px solid black; text-align:center;'>SGPA</th>
                      <th style='border:1px solid black; text-align:center;'>CGPA</th>
                      <th style='border:1px solid black; text-align:left;'>Status</th>";

                // Sem II
                echo "<th style='border:1px solid black; text-align:center;'>SGPA</th>
                      <th style='border:1px solid black; text-align:center;'>CGPA</th>
                      <th style='border:1px solid black; text-align:left;'>Status</th>";
                if ($ext) {
                    // summer
                    echo "<th style='border:1px solid black; text-align:center;'>SGPA</th>
                      <th style='border:1px solid black; text-align:center;'>CGPA</th>
                      <th style='border:1px solid black; text-align:left;'>Status</th>";
                }

                $cl++;
            }
            echo "</thead><tbody>";
            $count = 0;
            foreach ($gsets as $gset) {

                $stds = \backend\models\WGroupAssignment::find()->where(['gas_gset_relId' => $gset->id])->all();
                $cGset = NULL;

                if ($stds) {
                    foreach ($stds as $std) {
                        $student = $std->gasStdRel;
                        $stdRow = "";
                        $showRow = true;
                        if ($student) {
                            $cGset = CCF::currentStudentGroup($student);
                        } else {
                            continue;
                        }
                        $stdRow .= "<tr onclick='adClas({$count})' id='row_{$count}'>";
                        $stdRow .= "<td style='border:1px solid black;'>" . ++$count . "</td>";
                        $stdRow .= "<td style='border:1px solid black; text-align:center;'>{$student->std_regId}</td>
                            <td style='border:1px solid black; text-align:left;'>{$student->stdUserRel->first_name} {$student->stdUserRel->middle_name} {$student->stdUserRel->last_name} <b>({$student->stdDeptRel->dept_code})</b></td>";
                        $c = 1;
                        while ($c <= $clslvlObj->clslvl_num) {

                            if (!($cGset)) {
                                continue;
                            }
                            $semGrade1 = $this->getSemGrade($student->id, $c, "I", $cGset);
                            // echo "Calling getSemgrade....";
                            // Y-I
                            $set1 = isset($semGrade1[3]) ? 'bold' : 'normal';
                            $stdRow .= "<td style='border:1px solid black; text-align:center; font-weight:{$set1};'>$semGrade1[0]</td>";
                            $stdRow .= "<td style='border:1px solid black; text-align:center; font-weight:{$set1};'>$semGrade1[1]</td>";
                            $stdRow .= "<td style='border:1px solid black; text-align:left; font-weight:{$set1};'>$semGrade1[2]</td>";

                            // Y-II
                            $semGrade2 = $this->getSemGrade($student->id, $c, "II", $cGset);
                            $set2 = isset($semGrade2[3]) ? 'bold' : 'normal';
                            $stdRow .= "<td style='border:1px solid black; text-align:center; font-weight:{$set2};'>$semGrade2[0]</td>";
                            $stdRow .= "<td style='border:1px solid black; text-align:center; font-weight:{$set2};'>$semGrade2[1]</td>";
                            $stdRow .= "<td style='border:1px solid black; text-align:left; font-weight:{$set2};'>$semGrade2[2]</td>";

                            if ($ext) {
                                // Y-III (Summer)
                                $set3 = isset($semGrade3[3]) ? 'bold' : 'normal';
                                $semGrade3 = $this->getSemGrade($student->id, $c, "Summer", $cGset);
                                $stdRow .= "<td style='border:1px solid black; text-align:center; font-weight:{$set3};'>$semGrade3[0]</td>";
                                $stdRow .= "<td style='border:1px solid black; text-align:center; font-weight:{$set3};'>$semGrade3[1]</td>";
                                $stdRow .= "<td style='border:1px solid black; text-align:left; font-weight:{$set3};'>$semGrade3[2]</td>";
                            }

                            // $showRow = (isset($semGrade3[3]) || isset($semGrade2[3]) || isset($semGrade1[3]))?
                            //     ():();
                            $showRow = isset($semGrade3[3]) ? (($semGrade3[4] == 2 || $semGrade3[4] == 3 || $semGrade3[4] == 0) ? true : false) : (isset($semGrade2[3]) ? (($semGrade2[4] == 2 || $semGrade2[4] == 3 || $semGrade2[4] == 0) ? true : false) : (isset($semGrade1[3]) ? (($semGrade1[4] == 2 || $semGrade1[4] == 3 || $semGrade1[4] == 0) ? true : false) :
                                false));
                            $c++;
                        }
                        $stdRow .= "</tr>";
                        if ($showRow) {
                            echo $stdRow;
                        }
                    }
                }
            }
            echo "</tbody></table>";
        } else {
            echo "No Result.";
        }
    }

    private function getSemGrade($stdId, $clslvl, $sem, $cGset)
    {
        // echo "Call to getSemGrade Safe!....<br>";
        $semObject = \backend\models\Semester::findOne(['semester_name' => $sem]);
        $clslvlObj = \backend\models\ClassLevel::findOne(['clslvl_num' => $clslvl, 'clslvl_program_relId' => $cGset->gs_prog_relId]);
        $stdObject = \backend\models\Student::findOne($stdId);

        $semregObj = new SemesterRegistration2Detail();
        $semReport = $semregObj->getSemregRecord($stdId, $clslvlObj->id, $semObject->id, $cGset);

        if (is_array($semReport)) {
            $semReport[0] = is_numeric($semReport[0]) ? sprintf("%0.2f", $semReport[0]) : $semReport[0];
            $semReport[1] = is_numeric($semReport[1]) ? sprintf("%0.2f", $semReport[1]) : $semReport[1];
            return $semReport;
        }
        return "Could NOT find Semester Status";
    }


    private function getDeptIndex($semregModel)
    {
        return \backend\models\Student::findOne($semregModel->semreg_std_relId)->std_dept_relId;
    }

    public function actionOutstandingStudents()
    {
        $model = new StudentSemesterStatusHistory();
        return $this->render('report_outstanding', ['model' => $model]);
    }

    public function actionGetStudentSemesterStatus2Report()
    {
        $params = isset($_GET['params']) && !empty($_GET['params']) ? (string)$_GET['params'] : die("Invalid Parameters");

        $params = json_decode($params);
        if (is_object($params)) {
            $acadYearId = isset($params->acadYearId) && !empty($params->acadYearId) ? intval($params->acadYearId) : die('Select Academic  year');
            $semId = isset($params->semId) && !empty($params->semId) ? intval($params->semId) : die('Select Semester');
            $admId = isset($params->admId) && !empty($params->admId) ? intval($params->admId) : die('Select Admission');
            $progId = isset($params->progId) && !empty($params->progId) ? intval($params->progId) : die('Uknown Program');
            $clslvlId = isset($params->clslvlId) && !empty($params->clslvlId) ? intval($params->clslvlId) : die('Uknown Class Year.');
            $gsetId = isset($params->gsetId) && !empty($params->gsetId) ? intval($params->gsetId) : die('Uknown Group.');
            return $this->formReport($acadYearId, $semId, $admId, $progId, $clslvlId, $gsetId);
        } else {
            die("Invalid Parameters set....");
        }
    }

    // returns list of students' semsetr status per their group
    public function actionGetStudentSemesterStatus2()
    {
        // initialize current acdaYera and Semester

        if (!(Yii::$app->user->can('Registrar') || Yii::$app->user->can('clerk') || Yii::$app->user->can('Admin'))) {
            die('<p class="bg-warning">Un-privilaged user.</p>');
        }
        // $this->__constructor();

        // obtain the passes post datas / params
        // $this->semId
        // $this->acadYearId
        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId']) ? intval($_POST['acadYearId']) : die('acadYearId Error..');
        $semId = isset($_POST['semId']) && !empty($_POST['semId']) ? intval($_POST['semId']) : die('semId Error..');
        $admId = isset($_POST['admId']) && !empty($_POST['admId']) ? intval($_POST['admId']) : die('Uknown Admission');
        $progId = isset($_POST['progId']) && !empty($_POST['progId']) ? intval($_POST['progId']) : die('Uknown Program');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId']) ? intval($_POST['clslvlId']) : die('Uknown Class Year.');
        $gsetId = isset($_POST['gsetId']) && !empty($_POST['gsetId']) ? intval($_POST['gsetId']) : die('Uknown Group/Section.');


        return $this->formReport($acadYearId, $semId, $admId, $progId, $clslvlId, $gsetId);
    }


    private function formReport($acadYearId, $semId, $admId, $progId, $clslvlId, $gsetId)
    {

        $gset = WGroupSetup::findOne($gsetId);

        if (!($gset)) {
            die('Invalid Group Setup for your selection.');
        }

        $gAssignments = \backend\models\WGroupAssignment::find()
            ->innerJoin('student', 'student.id = w_group_assignment.gas_std_relId')
            ->innerJoin('user', 'user.id = student.std_user_relId')
            ->where(['gas_gset_relId' => $gset->id])
            ->orderBy('user.first_name')
            ->all();
        $statusTable = '';

        if ($gAssignments) {
            $statusTable .= "<table id='reportTable' class='table table-bordered table-striped' style='font-size:9pt;'  cellspacing='0' cellpadding='1' >";

            $admission1 = \backend\models\Admission::findOne(['id' => $admId]);
            $program1 = \backend\models\Program::findOne(['id' => $progId]);
            $clslvl1 = \backend\models\ClassLevel::findOne(['id' => $clslvlId]);
            $acadYear1 = \backend\models\SchoolYear::findOne(['id' => $acadYearId]);
            $semester1 = \backend\models\Semester::findOne(['id' => $semId]);

            $spec = $gset->gs_spec_relId ? ' - ' . $gset->gsSpecRel->spec_name : '';

            $statusTable .= "<caption style='text-align:center;'>
                <b><u>" . strtoupper(Yii::$app->params['client']) . ", " . strtoupper(Yii::$app->params['college']) . "</u></b><br>
                <b><u>Student Semester Status</u></b><br>

                Academic Year: <b> <u><i>{$acadYear1->sch_year_desc}</u></i></b>
                Semester: <b><u><i>{$semester1->semester_name}</u></i></b><br><br>

                Program: <b><u><i>{$admission1->admission_name} - {$program1->program_name}</u></i></b><br>
                Department: <b><u><i>{$gset->gsDeptRel->dept_name} {$spec}</u></i></b><br>

                Class Level: <b><u><i>{$clslvl1->getClslvlDisp()}</u></i></b>               
            </caption>";
            $statusTable .= "<thead style='background-color:#ace; border:1px solid black; font-size: 8pt; vertical-align:middle;'>
                <th style='border:1px solid black; vertical-align:middle;' rowspan=2>#</th>
                <th style='border:1px solid black; vertical-align:middle;' rowspan=2>RegId</th>
                <th style='border:1px solid black; vertical-align:middle;' rowspan=2>Full Name</th>
                <th style='border:1px solid black; text-align:center;' colspan=3>Prev. Semester</th>
                <th style='border:1px solid black; text-align:center;' colspan=3>This Semester</th>
                <th style='border:1px solid black; vertical-align: middle;' rowspan=2>NG's</th>
                <th style='border:1px solid black; vertical-align: middle;' rowspan=2>F's</th>
                </thead>";

            $statusTable .= "<thead style='background-color:#ace; font-size: 8pt;'>
                <th style='border:1px solid black;'></th>
                <th style='border:1px solid black;'></th>
                <th style='border:1px solid black;'></th>

                <th style='border:1px solid black;'>SGPA</th>
                <th style='border:1px solid black;'>CGPA</th>
                <th style='border:1px solid black;'>Status</th>

                <th style='border:1px solid black;'>SGPA</th>
                <th style='border:1px solid black;'>CGPA</th>
                <th style='border:1px solid black;'>Status</th>
                <th style='border:1px solid black;'></th>
                <th style='border:1px solid black;'></th>
                </thead><tbody>";
            $count = 0;
            $statusPrevCount = [];
            $statusThisCount = [];
            foreach ($gAssignments as $assignment) {
                $student = $assignment->gasStdRel;
                $usr = \backend\models\User::findOne(['id' => $student->std_user_relId]);
                $gset = NULL;
                $gset = CCF::getStudentGroupHistoryPublic($clslvlId, $semId, $student->id);

                $prevCGPA = NULL;
                $prevSGPA = '-';

                $semesterStatus = NULL;
                $ssId = NULL;
                $semesterStatusCalcType = '';
                $srd2Model = new \frontend\models\SemesterRegistration2Detail();



                $satatus1 = 'Uknown';
                $gradeHistory = CCF::getSemesterGrade($clslvlId, $semId, $student->id);
                $SGPA = 0.000;
                $CGPA = 0.000;


                // echo "<pre>";
                // var_dump($gradeHistory[1]);
                // die();

                $semesterStatus = 'Uknown';
                $ssId = 'Uknown';
                $semesterStatusCalcType = '';
                $prevStat = NULL;
                $ngs = NULL;

                $prevSGPA = "";
                $prevCGPA = "";
                $satatus2 = "";
                $SGPA = "";
                $CGPA = "";
                $ngsn = "";
                $satatus1 = "";
                $ngsf = "";
                if (is_array($gradeHistory) && is_array($gradeHistory[1])) {
                    if (isset($gset)) {
                        $satatus1 = $srd2Model->getSemregRecord($student->id, $gset->gs_clslvl_relId, $gset->gs_sem_relId, $gset, NULL)[2];

                        // var_dump($satatus1);die
                        // if status and student's profile is NOT updated in such a way, propagate it.
                        $semreg = $srd2Model->srd2SemregRel;

                        // if ($semreg->std_semester_status != $satatus1) {
                        //     $semreg->std_semester_status = $satatus1;
                        //     $semreg->update();
                        // }

                        $satatus2 = $srd2Model->getSemregRecordPrev($student->id, $gset, NULL)[2];
                        $CGPA = is_numeric($gradeHistory[1][2][0]) ? sprintf("%0.2f", round($gradeHistory[1][2][0], 2, PHP_ROUND_HALF_UP)) : (sprintf("%0.2f", round(($gradeHistory[1][2][1] / $gradeHistory[1][2][2]), 2, PHP_ROUND_HALF_UP)));
                        $ss21 = is_numeric($gradeHistory[1][1][1]) ? sprintf("%0.2f", round($gradeHistory[1][1][1], 2, PHP_ROUND_HALF_UP)) : $gradeHistory[1][1][1];
                        $SGPA = is_numeric($gradeHistory[1][0][0]) ? sprintf("%0.2f", round($gradeHistory[1][0][0], 2, PHP_ROUND_HALF_UP)) : $gradeHistory[1][0][0]; //this SGPA

                        $prevSGPA = is_numeric($gradeHistory[1][1][0]) ? sprintf("%0.2f", round($gradeHistory[1][1][0], 2, PHP_ROUND_HALF_UP)) : '';
                        $prevCGPA = $ss21;
                    } else {
                        $satatus1 = 'GROUP ERROR';
                    }
                }

                if ($gset) {
                    $ngs = $srd2Model->getNGSFS($student, $gset);
                    $ngsn = $ngs['ngs'];
                    $ngsf = $ngs['Fs'];
                }

                $dept = is_null($gset) ? '-' : $gset->gsDeptRel->dept_code;
                $spec = isset($gset->gs_spec_relId) ? $gset->gsSpecRel->spec_name : '-';


                if (isset($_POST['requiredStatus']) && !empty($_POST['requiredStatus']) && ($ssId != intval($_POST['requiredStatus']))) {
                    continue;
                }


                $statusTable .=  "<tr>
                <td style='border:1px solid black;'>" . ++$count . "</td>
                <td style='border:1px solid black;'>" . $student->std_regId . "</td>
                <td style='border:1px solid black;'>" . $usr->getFullName() . " (<b>{$dept}</b>)</td>

                <td style='text-align:center; border:1px solid black;'>{$prevSGPA}</td>
                <td style='text-align:center; border:1px solid black;'>{$prevCGPA}</td>
                <td style='text-align:left; border:1px solid black;'>{$satatus2}</td>

                <td style='text-align:center; border:1px solid black;'>{$SGPA}</td>
                <td style='text-align:center; border:1px solid black;'>{$CGPA}</td>
                <td style='text-align:left; border:1px solid black;'>{$satatus1}</td>

                <td style='text-align:left; border:1px solid black;'>{$ngsn}</td>
                <td style='text-align:left; border:1px solid black;'>{$ngsf}</td>
                </tr>";
                // $count++;
                if (isset($statusPrevCount[$satatus2])) {
                    $statusPrevCount[$satatus2] += 1;
                } else {
                    $statusPrevCount[$satatus2] = 1;
                }

                if (isset($statusThisCount[$satatus1])) {
                    $statusThisCount[$satatus1] += 1;
                } else {
                    $statusThisCount[$satatus1] = 1;
                }
            }
            $summery = $this->prepareSummery($statusPrevCount, $statusThisCount);
            $statusTable .= "<tr style='background-color:#dde' class='non-print'>
                        <td style='border:1px solid black;' colspan=11>{$count} record found.</td>
                    </tr><tr></tr>";
            $statusTable .= "<tr style='background-color:#A98;'>
                        <td></td>
                        <td></td>
                        <td style='border:1px solid black;' colspan=7>" . $summery . "</td>
                        <td></td>
                        <td></td>
                    </tr>";

            $statusTable .= "</tbody></table>";

            return $statusTable;
        } else {
            die('No student found.');
        }
    }

    private function formReport2($gsetId){
        $gset = WGroupSetup::findOne($gsetId);

        if (!($gset)) {
            die('Invalid Group Setup for your selection.');
        }

        $gAssignments = \backend\models\WGroupAssignment::find()
            ->innerJoin('student', 'student.id = w_group_assignment.gas_std_relId')
            ->innerJoin('user', 'user.id = student.std_user_relId')
            ->where(['gas_gset_relId' => $gset->id])
            ->orderBy('user.first_name')
            ->all();
        $statusTable = '';

        if ($gAssignments) {
            $statusTable .= "<table id='reportTable' class='table table-bordered table-striped' style='font-size:9pt;'  cellspacing='0' cellpadding='1' >";

            $admission1 =$gset->gsAdmRel;
            $program1 = $gset->gsProgRel;
            $clslvl1 = $gset->gsClslvlRel;
            $acadYear1 = $gset->gsAcadYearRel;
            $semester1 = $gset->gsSemRel;

            $spec = $gset->gs_spec_relId ? ' - ' . $gset->gsSpecRel->spec_name : '';

            $statusTable .= "<caption style='text-align:center;'>
                <b><u>" . strtoupper(Yii::$app->params['client']) . ", " . strtoupper(Yii::$app->params['college']) . "</u></b><br>
                <b><u>Student Semester Status</u></b><br>

                Academic Year: <b> <u><i>{$acadYear1->sch_year_desc}</u></i></b>
                Semester: <b><u><i>{$semester1->semester_name}</u></i></b><br><br>

                Program: <b><u><i>{$admission1->admission_name} - {$program1->program_name}</u></i></b><br>
                Department: <b><u><i>{$gset->gsDeptRel->dept_name} {$spec}</u></i></b><br>

                Class Level: <b><u><i>{$clslvl1->getClslvlDisp()}</u></i></b>               
            </caption>";
            $statusTable .= "<thead style='background-color:#ace; border:1px solid black; font-size: 8pt; vertical-align:middle;'>
                <th style='border:1px solid black; vertical-align:middle;' rowspan=2>#</th>
                <th style='border:1px solid black; vertical-align:middle;' rowspan=2>RegId</th>
                <th style='border:1px solid black; vertical-align:middle;' rowspan=2>Full Name</th>
                <th style='border:1px solid black; text-align:center;' colspan=3>Prev. Semester</th>
                <th style='border:1px solid black; text-align:center;' colspan=3>This Semester</th>
                <th style='border:1px solid black; vertical-align: middle;' rowspan=2>NG's</th>
                <th style='border:1px solid black; vertical-align: middle;' rowspan=2>F's</th>
                </thead>";

            $statusTable .= "<thead style='background-color:#ace; font-size: 8pt;'>
                <th style='border:1px solid black;'></th>
                <th style='border:1px solid black;'></th>
                <th style='border:1px solid black;'></th>

                <th style='border:1px solid black;'>SGPA</th>
                <th style='border:1px solid black;'>CGPA</th>
                <th style='border:1px solid black;'>Status</th>

                <th style='border:1px solid black;'>SGPA</th>
                <th style='border:1px solid black;'>CGPA</th>
                <th style='border:1px solid black;'>Status</th>
                <th style='border:1px solid black;'></th>
                <th style='border:1px solid black;'></th>
                </thead><tbody>";
            $count = 0;
            $statusPrevCount = [];
            $statusThisCount = [];
            foreach ($gAssignments as $assignment) {
                $student = $assignment->gasStdRel;
                $srd2s = $assignment->gasGsetRel->getStudentSemesterRegistrations($assignment->gas_std_relId);
                $semreg = $srd2s[0]->srd2SemregRel;
                $usr = $student->stdUserRel;
                // $gset = NULL;
                // $gset = CCF::getStudentGroupHistoryPublic($clslvlId, $semId, $student->id);

                $prevCGPA = NULL;
                $prevSGPA = '-';

                $semesterStatus = NULL;
                $ssId = NULL;
                $semesterStatusCalcType = '';
                $srd2Model = new SemesterRegistration2Detail();



                $satatus1 = 'Uknown';
                $gradeHistory = CCF::getSemesterGrade($gset->gs_clslvl_relId, $gset->gs_sem_relId, $student->id);
                $SGPA = 0.000;
                $CGPA = 0.000;


                // echo "<pre>";
                // var_dump($gradeHistory[1]);
                // die();

                $semesterStatus = 'Uknown';
                $ssId = 'Uknown';
                $semesterStatusCalcType = '';
                $prevStat = NULL;
                $ngs = NULL;

                $prevSGPA = "";
                $prevCGPA = "";
                $satatus2 = "";
                $SGPA = "";
                $CGPA = "";
                $ngsn = "";
                $satatus1 = "";
                $ngsf = "";
                if (is_array($gradeHistory) && is_array($gradeHistory[1])) {
                    if (isset($gset)) {
                        $satatus1 = $srd2s->getSemregRecord($student->id, $gset->gs_clslvl_relId, $gset->gs_sem_relId, $gset, NULL)[2];

                        // var_dump($satatus1);die
                        // if status and student's profile is NOT updated in such a way, propagate it.
                        // $semreg = $srd2Model->srd2SemregRel;

                        // if ($semreg->std_semester_status != $satatus1) {
                        //     $semreg->std_semester_status = $satatus1;
                        //     $semreg->update();
                        // }

                        $satatus2 = $srd2Model->getSemregRecordPrev($student->id, $gset, NULL)[2];
                        $CGPA = is_numeric($gradeHistory[1][2][0]) ? sprintf("%0.2f", round($gradeHistory[1][2][0], 2, PHP_ROUND_HALF_UP)) : (sprintf("%0.2f", round(($gradeHistory[1][2][1] / $gradeHistory[1][2][2]), 2, PHP_ROUND_HALF_UP)));
                        $ss21 = is_numeric($gradeHistory[1][1][1]) ? sprintf("%0.2f", round($gradeHistory[1][1][1], 2, PHP_ROUND_HALF_UP)) : $gradeHistory[1][1][1];
                        $SGPA = is_numeric($gradeHistory[1][0][0]) ? sprintf("%0.2f", round($gradeHistory[1][0][0], 2, PHP_ROUND_HALF_UP)) : $gradeHistory[1][0][0]; //this SGPA

                        $prevSGPA = is_numeric($gradeHistory[1][1][0]) ? sprintf("%0.2f", round($gradeHistory[1][1][0], 2, PHP_ROUND_HALF_UP)) : '';
                        $prevCGPA = $ss21;
                    } else {
                        $satatus1 = 'GROUP ERROR';
                    }
                }

                if ($gset) {
                    $ngs = $srd2Model->getNGSFS($student, $gset);
                    $ngsn = $ngs['ngs'];
                    $ngsf = $ngs['Fs'];
                }

                $dept = is_null($gset) ? '-' : $gset->gsDeptRel->dept_code;
                $spec = isset($gset->gs_spec_relId) ? $gset->gsSpecRel->spec_name : '-';


                if (isset($_POST['requiredStatus']) && !empty($_POST['requiredStatus']) && ($ssId != intval($_POST['requiredStatus']))) {
                    continue;
                }


                $statusTable .=  "<tr>
                <td style='border:1px solid black;'>" . ++$count . "</td>
                <td style='border:1px solid black;'>" . $student->std_regId . "</td>
                <td style='border:1px solid black;'>" . $usr->getFullName() . " (<b>{$dept}</b>)</td>

                <td style='text-align:center; border:1px solid black;'>{$prevSGPA}</td>
                <td style='text-align:center; border:1px solid black;'>{$prevCGPA}</td>
                <td style='text-align:left; border:1px solid black;'>{$satatus2}</td>

                <td style='text-align:center; border:1px solid black;'>{$SGPA}</td>
                <td style='text-align:center; border:1px solid black;'>{$CGPA}</td>
                <td style='text-align:left; border:1px solid black;'>{$satatus1}</td>

                <td style='text-align:left; border:1px solid black;'>{$ngsn}</td>
                <td style='text-align:left; border:1px solid black;'>{$ngsf}</td>
                </tr>";
                // $count++;
                if (isset($statusPrevCount[$satatus2])) {
                    $statusPrevCount[$satatus2] += 1;
                } else {
                    $statusPrevCount[$satatus2] = 1;
                }

                if (isset($statusThisCount[$satatus1])) {
                    $statusThisCount[$satatus1] += 1;
                } else {
                    $statusThisCount[$satatus1] = 1;
                }
            }
            $summery = $this->prepareSummery($statusPrevCount, $statusThisCount);
            $statusTable .= "<tr style='background-color:#dde' class='non-print'>
                        <td style='border:1px solid black;' colspan=11>{$count} record found.</td>
                    </tr><tr></tr>";
            $statusTable .= "<tr style='background-color:#A98;'>
                        <td></td>
                        <td></td>
                        <td style='border:1px solid black;' colspan=7>" . $summery . "</td>
                        <td></td>
                        <td></td>
                    </tr>";

            $statusTable .= "</tbody></table>";

            return $statusTable;
        } else {
            die('No student found.');
        }
    }

    private function prepareSummery($statusPrevCount, $statusThisCount)
    {
        $rep = "";
        // var_dump($statusThisCount);
        // return;
        if (is_array($statusThisCount)) {
            $rep .= "<table class='table table-condensed table-sm table-bordered table-hover' style='border:1px solid black; font-size:8pt; background-color;inherit;' cellspacing='0' cellpadding='1' >";
            $rep .= "<caption style='text-align:center; font-weight:normal; color:#000; background-color:inherit;'>Status Summery</cation>";

            $rep .= "<thead>
                        <th style=' border: 1px solid black;'>Status</th>
                        <th style=' border: 1px solid black;'>Prev. Semester</th>
                        <th style=' border: 1px solid black;'>This Semester</th>
                        <th style=' border: 1px solid black;'>Change</th>
                    </thead><tbody>";
            $valueT = 0;
            $pValueT = 0;
            foreach ($statusThisCount as $keyStatus => $count) {
                $value = $count;
                $pValue = isset($statusPrevCount[$keyStatus]) ? $statusPrevCount[$keyStatus] : 0;
                $change = $value - $pValue;
                $rep .= "<tr>
                            <td style=' border: 1px solid black;'>{$keyStatus}</td>
                            <td style='text-align:center; border: 1px solid black;'>{$pValue}</td>
                            <td style='text-align:center; border: 1px solid black;'>{$value}</td>
                            <td style='text-align:center; border: 1px solid black;'>{$change}</td>
                        </tr>";
                $valueT += $value;
                $pValueT += $pValue;
            }

            $rep .= "<tr style='font-weight:bold; font-style:italic; text-decoration:underline;'>
                    <td style=' border: 1px solid black;text-align: right;'>Total</td>
                    <td style='text-align:center; border: 1px solid black;'>{$pValueT}</td>
                    <td style='text-align:center; border: 1px solid black;'>{$valueT}</td>
                    <td style='text-align:center; border: 1px solid black;'>-</td>
                </tr>";
            $rep .= "<tbody></table>";
        }

        return $rep;
    }
    protected function checkSemStatExists($studentObject)
    {
        // check if the student semester status set before completing the semester.
        // Return the student semester status object if exists, OR NULL;

        $this->__constructor();

        return \backend\models\StudentSemesterStatusHistory::find()->where([
            'sssh_acadYear_relId' => $this->acadYearId,
            'sssh_sem_relId' => $this->semId,
            'sssh_std_relId' => $studentObject->id,
        ])->innerJoin('student_semester_status_name', 'student_semester_status_name.id = student_semester_status_history.sssh_sssn_relId')
            ->andWhere([
                'student_semester_status_name.sssn_name' => [
                    'Academic Dismissal',
                    'Deciplinary Dismissal',
                    'Personal Dropout',
                    'Defector'
                ]
            ])
            ->andWhere(['IS NOT', 'sssh_sssn_relId', NULL])
            ->distinct('student_semester_status_name.id')
            ->with('ssshSssnRel')
            ->one();
    }


    //save-record
    public function actionSaveRecord()
    {
        $checkedStdsString = isset($_POST['checkedStdsString']) && !empty($_POST['checkedStdsString']) ? $_POST['checkedStdsString'] : die('Students selection error');
        $students = json_decode($checkedStdsString, true);

        //var_dump($students);


        $total = count($students);
        $scount = 0;
        $fcount = 0;
        $message = 'Status Changr : <br>';
        foreach ($students as $key => $value) {
            //print_r();
            $value = (array)$value;
            $stdId = intval($key);
            if (empty($value)) {
                continue;
            }
            $statusId = $value[0];
            $clslvlId = $value[1];

            // default timecheck is current academic calender.
            $this->__constructor();

            $transaction = Yii::$app->db->beginTransaction();
            // check if exists before,
            try {
                $exists = StudentSemesterStatusHistory::findOne([
                    'sssh_std_relId' => $stdId,
                    'sssh_acadYear_relId' => $this->acadYearId,
                    'sssh_sem_relId' => $this->semId,
                    'sssh_clslvl_relId' => $clslvlId
                ]);
                // if exist update
                if ($exists) {
                    // update if status change exists,
                    if ($exists->sssh_sssn_relId != $statusId) {
                        $result = Yii::$app->db->createCommand()
                            ->update(
                                'student_semester_status_history',
                                [
                                    'sssh_sssn_relId' => $statusId,
                                ],
                                [
                                    'id' => $exists->id,
                                ]
                            )
                            ->execute();
                        if ($result) {
                            // update student semeetser status.
                            if ($this->updateStudentProfile($stdId, $statusId)) {

                                $message .= 'Status change Saved succesful.';
                                $scount++;
                                $transaction->commit();
                            }
                        } else {
                            throw new ErrorException("ERROR while setting student semster status change. Please try again.", 1);
                        }
                    }
                } else {
                    // if not insert new.
                    $model = new StudentSemesterStatusHistory();
                    $model->sssh_std_relId = $stdId;
                    $model->sssh_sssn_relId = $statusId;
                    $model->sssh_acadYear_relId = $this->acadYearId;
                    $model->sssh_sem_relId = $this->semId;
                    $model->sssh_clslvl_relId = $clslvlId;

                    $model->sssh_created_at = $model->sssh_updated_at = date('Y-m-d h:i:s');
                    $model->sssh_created_by  = $model->sssh_updated_by = Yii::$app->user->identity->id;
                    if ($model->save()) {
                        if ($this->updateStudentProfile($stdId, $statusId)) {
                            $scount++;
                            $message .= 'Status change Saved succesful.';
                            $transaction->commit();
                        }
                    } else {
                        throw new ErrorException("Failed to store new student semsetr status change.", 1);
                    }
                }
            } catch (Exception $e) {
                $transaction->rollBack();
                $fcount++;
                $message .= $e->getMessage() . '<br>';
            }
            // $transaction->commit();
        }
        $total = $scount + $fcount;
        die("<p class='bg-info'>{$scount} out of {$total} saved successfilly.<br>{$message}</p>");
    }
    //

    // update student profile depending on the semester status calculated.
    private function updateStudentProfile($stdId, $statusId)
    {
        $statusName = \backend\models\StudentSemesterStatusName::findOne(['id' => $statusId])->sssn_name;
        $result2 = Yii::$app->db->createCommand()
            ->update(
                'student',
                [
                    'std_semester_status' => $statusName,
                ],
                [
                    'id' => $stdId,
                ]
            )
            ->execute();
        if ($result2) {
            return TRUE;
        } else {
            throw new ErrorException("ERROR Occured while setting semetser status change to student profile.", 1);
        }
    }

    /**
     * Displays a single StudentSemesterStatusHistory model.
     * @param integer $id
     * @return mixed
     */
    public function actionView($id)
    {
        return $this->render('view', [
            'model' => $this->findModel($id),
        ]);
    }

    /**
     * Creates a new StudentSemesterStatusHistory model.
     * If creation is successful, the browser will be redirected to the 'view' page.
     * @return mixed
     */
    public function actionCreate()
    {
        $model = new StudentSemesterStatusHistory();

        if ($model->load(Yii::$app->request->post()) && $model->save()) {
            return $this->redirect(['view', 'id' => $model->id]);
        } else {
            return $this->render('create', [
                'model' => $model,
            ]);
        }
    }

    /**
     * Updates an existing StudentSemesterStatusHistory model.
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
     * Deletes an existing StudentSemesterStatusHistory model.
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
     * Finds the StudentSemesterStatusHistory model based on its primary key value.
     * If the model is not found, a 404 HTTP exception will be thrown.
     * @param integer $id
     * @return StudentSemesterStatusHistory the loaded model
     * @throws NotFoundHttpException if the model cannot be found
     */
    protected function findModel($id)
    {
        if (($model = StudentSemesterStatusHistory::findOne($id)) !== null) {
            return $model;
        } else {
            throw new NotFoundHttpException('The requested page does not exist.');
        }
    }
}
