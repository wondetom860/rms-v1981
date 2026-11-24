<?php

namespace frontend\controllers;
use yii;
use kartik\mpdf\Pdf;
use yii\filters\VerbFilter;
use backend\models\ScholarshipType;
use frontend\models\SemesterRegistration2;
use backend\models\StudentSemesterStatusHistory;
use frontend\models\SemesterRegistration2Detail;
use common\components\CommonControllerFunctions as CCF;

class EnrolledStudentsReportController extends \yii\web\Controller
{
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
                       'roles' => ['@'],
                   ],
               ]
           ]
        ];
    }
    public $acadYearId;
    public $semId;
    protected function __constructor(){

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }
    public function actionIndex()
    {
        $model = new StudentSemesterStatusHistory();
        return $this->render('index',['model'=>$model]);
    }

    public function actionPrintStudentList(){
        $params = isset($_GET['params']) && !empty($_GET['params'])?(string)$_GET['params']:die('Invalid Enquery');

        $params = json_decode($params);

        $message = "";
        $gsModel = NULL;
        $stdModel = NULL;
        if (!is_object($params)) {
            $message = "Invalid Enqury, Parameters Error!";
        }else{
            $acadYearId = isset($params->acadYearId) && !empty($params->acadYearId)?intval($params->acadYearId):die('Select Academic Year');
            $semId = isset($params->semId) && !empty($params->semId)?intval($params->semId):die('Select Semester');
            $admId = isset($params->admId) && !empty($params->admId)?intval($params->admId):die('Select Admission');
            $progId = isset($params->progId) && !empty($params->progId)?intval($params->progId):die('Select Program');
            $clslvlId = isset($params->clslvlId) && !empty($params->clslvlId)?intval($params->clslvlId):die('Select Class Year.');
            $deptId = isset($params->deptId) && !empty($params->deptId)?intval($params->deptId):die('Select Department.');
            $specId = isset($params->spec) && !empty($params->spec)?intval($params->spec):NULL;

            //'student.std_dept_relId'=>$deptId
             // 'student.std_dept_relId'=>$deptId
        $enrolledStudents = SemesterRegistration2::find()
            ->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')
            ->innerJoin('user','user.id = student.std_user_relId')
            ->innerJoin('semester_registration_2_detail','semester_registration_2_detail.srd2_semreg_relId=semester_registration_2.id')
            ->innerJoin('w_group_setup','w_group_setup.id=semester_registration_2_detail.srd2_group_relId')
            ->andFilterWhere([
                'semester_registration_2.semreg_acadYear_relId'=>$acadYearId,
                'semester_registration_2.semreg_semester_relId'=>$semId,
                'w_group_setup.gs_adm_relId'=>$admId,
                'w_group_setup.gs_prog_relId'=>$progId,
                'w_group_setup.gs_clslvl_relId' => $clslvlId,
                'w_group_setup.gs_dept_relId' => $deptId,
            ]);

        if (isset($_POST['spec']) && !empty($_POST['spec'])) {
            $enrolledStudents = $enrolledStudents->andFilterWhere(['w_group_setup.gs_spec_relId'=>$specId]);
        }

        if (isset($_POST['spon']) && !empty($_POST['spon'])) {
            $enrolledStudents = $enrolledStudents->andFilterWhere(['student.std_schlrshpType_relId'=>$_POST['spon']]);
        }

        $enrolledStudents = $enrolledStudents->orderBy(['first_name' => SORT_ASC,'middle_name'=> SORT_ASC,'last_name' => SORT_ASC])->all();

            $sIds = [];
            $gsetIds = [];
            if ($enrolledStudents && count($enrolledStudents) > 0) {
                foreach ($enrolledStudents as $ESkey) {
                    $sIds[] = $ESkey->semreg_std_relId;
                }
            }

            $acadYearModel = \backend\models\SchoolYear::findOne($acadYearId);
            $semModel = \backend\models\Semester::findOne($semId);
            $admModel = \backend\models\Admission::findOne($admId);
            $progModel = \backend\models\Program::findOne($progId);
            $deptModel = \backend\models\Department::findOne($deptId);
            $clslvlModel = \backend\models\ClassLevel::findOne($clslvlId);
            $specModel = isset($params->spec) && !empty($params->spec)?\backend\models\Specialization::findOne($specId):NULL;

            $stdModel = \backend\models\Student::find()
                ->innerJoin('user','user.id = student.std_user_relId')
                ->innerJoin('w_group_setup','w_group_setup.id = student.std_group_realId')
                ->innerJoin('w_group','w_group.id = w_group_setup.gs_group_relId')
                ->where(['student.id'=>$sIds]);

            if (isset($params->nat) && !empty($params->nat)) {
                $stdModel = $stdModel->andWhere(['user.nationality'=>intval($params->nat)]);
            }

            if (isset($params->gender) && !empty($params->gender)) {
                $stdModel = $stdModel->andWhere(['user.gender'=>$params->gender]);
            }

            if (isset($params->spon) && !empty($params->spon)) {
                $stdModel = $stdModel->andWhere(['student.std_schlrshpType_relId'=>$params->spon]);
            }

            if (isset($params->utype) && !empty($params->utype)) {
                $stdModel = $stdModel->andWhere(['user.user_type'=>$params->utype]);
            }

            $stdModel = $stdModel->orderBy([
                'user.first_name' => SORT_ASC,
                'user.middle_name' => SORT_ASC,
                'user.last_name' => SORT_ASC
            ])->all();
        }

        return $this->renderPartial('enrolled-student-list-all',
            [
                'stdModel'=>$stdModel,
                'message'=>$message,
                'acadYearModel'=>$acadYearModel,
                'semModel'=>$semModel,
                'amdModel'=>$admModel,
                'progModel'=>$progModel,
                'deptModel'=>$deptModel,
                'clslvlModel'=>$clslvlModel,
                'specModel'=>$specModel,
            ]);
    }


    public function actionGetGeneratedReport(){//get-generated-report
        $content = '';
        $semregId = isset($_GET['semregId']) && !empty($_GET['semregId']) ? intval($_GET['semregId']):die('Uknown Semetser Registration Entry...');
        // return $this->renderPartial('/semester-registration/__report/table-with-watermark.htm');

        $content = "";
        return $this->renderPartial('student_srd_report',['content'=> $semregId]);

    }
 

    public function actionGetEnrolledStudentList(){
         if (Yii::$app->user->can('Commandant') || Yii::$app->user->can('UniversityRegistrar') || Yii::$app->user->can('Registrar') ||Yii::$app->user->can('Admin') || Yii::$app->user->can('clerk') || Yii::$app->user->can('AcademicDean')) {
            // safe access by privilages users..
        }else{
            die('<p class="bg-warning">Un-privilaged user.</p>');
        }
        $this->__constructor();

        // obtain the passed post datas / params
        // $this->semId
        // $this->acadYearId
        $acadYearId = isset($_POST['acadYearId']) && !empty($_POST['acadYearId'])?intval($_POST['acadYearId']):die('Select Academic Year');
        $semId = isset($_POST['semId']) && !empty($_POST['semId'])?intval($_POST['semId']):die('Select Semester');
        $admId = isset($_POST['admId']) && !empty($_POST['admId'])?intval($_POST['admId']):die('Select Admission');
        $progId = isset($_POST['progId']) && !empty($_POST['progId'])?intval($_POST['progId']):die('Select Program');
        $clslvlId = isset($_POST['clslvlId']) && !empty($_POST['clslvlId'])?intval($_POST['clslvlId']):die('Select Class Year.');
        $deptId = isset($_POST['deptId']) && !empty($_POST['deptId'])?intval($_POST['deptId']):die('Select Department.');
        $specId = isset($_POST['spec']) && !empty($_POST['spec'])?intval($_POST['spec']):NULL;
        $nat = isset($_POST['nat']) && !empty($_POST['nat'])?intval($_POST['nat']):NULL;
        $gender = isset($_POST['gender']) && !empty($_POST['gender'])?$_POST['gender']:NULL;
        $spon = isset($_POST['spon']) && !empty($_POST['spon'])?$_POST['spon']:NULL;
        $utype = isset($_POST['utype']) && !empty($_POST['utype'])?$_POST['utype']:NULL;

        // 'student.std_dept_relId'=>$deptId
        $enrolledStudents = SemesterRegistration2::find()
        ->innerJoin('student','student.id = semester_registration_2.semreg_std_relId')
        ->innerJoin('user','user.id = student.std_user_relId')
        ->innerJoin('semester_registration_2_detail','semester_registration_2_detail.srd2_semreg_relId=semester_registration_2.id')
        ->innerJoin('w_group_setup','w_group_setup.id=semester_registration_2_detail.srd2_group_relId')
        ->andFilterWhere([
            'semester_registration_2.semreg_acadYear_relId'=>$acadYearId,
            'semester_registration_2.semreg_semester_relId'=>$semId,
            'w_group_setup.gs_adm_relId'=>$admId,
            'w_group_setup.gs_prog_relId'=>$progId,
            'w_group_setup.gs_clslvl_relId' => $clslvlId,
            'w_group_setup.gs_dept_relId' => $deptId,
        ]);

        if (isset($_POST['spec']) && !empty($_POST['spec'])) {
            $enrolledStudents = $enrolledStudents->andFilterWhere(['w_group_setup.gs_spec_relId'=>$specId]);
        }

        if (isset($_POST['spon']) && !empty($_POST['spon'])) {
            $enrolledStudents = $enrolledStudents->andFilterWhere(['student.std_schlrshpType_relId'=>$spon]);
        }

        $enrolledStudents = $enrolledStudents
            ->orderBy([
                'first_name'=>SORT_ASC,
                'middle_name'=>SORT_ASC,
                'last_name'=>SORT_ASC,
            ])->all();

        $table = "";
        if ($enrolledStudents && count($enrolledStudents) > 0) {
            $adm = \backend\models\Admission::findOne($admId);
            $prog = \backend\models\Program::findOne($progId);
            $dept = \backend\models\Department::findOne($deptId);
            $clslvl = \backend\models\ClassLevel::findOne($clslvlId);
            $spec = isset($_POST['spec']) && !empty($_POST['spec']) && $_POST['spec'] != 0?\backend\models\Specialization::findOne(intval($_POST['spec']))->spec_name:'-';
            $table .= "<table class='table table-bordered table-striped table-condensed table-hover' style='font-size:10pt;'>";
            $table .= "<caption style='text-align:center;'>
                        <h4><u>Enrolled Students List</u></h4>
                        <p>
                        <b>Admission:</b><u>{$adm->admission_name}</u>&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>Program:</b><u>{$prog->program_name}</u><br>
                        <b>Department:</b><u>{$dept->dept_name}</u>&nbsp;&nbsp;&nbsp;&nbsp;&nbsp;
                        <b>Class Level:</b><u>{$clslvl->getClslvlDisp()}</u><br>
                        <b>Specialization:</b><u>{$spec}
                        </p>
                    </caption>";
            $table .= "<thead style='background-color:#8D0;'>
                    <th>S/N</th>
                    <th>REG. No</th>
                    <th>Full Name</th>
                    <th>Gender</th>
                    <th>Student Type</th>
                    <th># Enrolled Courses</th>
                    <th># Added Courses</th>
                    <th class='non-print'>Generate Report</th>
                    <th class='non-print'>Remark</th>
                    </thead><tbody>";
                    $count = 0;
            foreach ($enrolledStudents as $key) {
                // $key = $key2->srd2SemregRel;
                // get number of courses enrolled by each student.
                $std = \backend\models\Student::findOne($key->semreg_std_relId);
                $user = \backend\models\User::findOne($std->std_user_relId);

                if (isset($_POST['gender']) && !empty($_POST['gender'])) {
                    if ($user->gender != $gender) {
                        continue;
                    }
                }

                if (isset($_POST['nat']) && !empty($_POST['nat'])) {
                    if ($user->nationality != $nat) {
                        continue;
                    }
                }

                if (isset($_POST['utype']) && !empty($_POST['utype'])) {
                    if ($user->user_type != $utype) {
                        continue;
                    }
                }
                $courseNum = $this->getEnrolledCourse($key->id,$key->semreg_std_relId);
                $genReport = $this->generateSemregReport($key);
                $genButton = "";

                $sc = ScholarshipType::findOne($std->std_schlrshpType_relId);
                
                if ($courseNum[3] > 0) {
                    if (is_array($genReport) && $genReport[0] == false && 0) {
                        $genButton = "<button class='btn btn-success btn-xs' title='{$genReport[1]}' onclick='generateReport(\"$genReport[1]\",{$key->id});return false;'>Generate Report?</button> ";
                    }else{
                        // $fname = (string)''.\Yii::$app->basePath .'\web\reports\\'.$genReport[1];
                        $fname = "";
                        // alreday generated the report. Ask for re-generating
                        $genButton = " <button class='btn btn-success btn-xs' title='Report alreday generated, Show it? {$fname}' onclick='showReport({$key->id});return false;'>Show?</button> ";
                    }
                }else{
                    $genButton = "$courseNum[2]";
                }
                $table .= "<tr>
                            <td>".++$count."</td>
                            <td>{$std->std_regId}</td>
                            <td>{$user->getFullName()}</td>
                            <td>(<b>{$user->gender}</b>)</td>
                            <td>{$user->user_type}({$sc->schType_name})</td>
                            <td>{$courseNum[0]}</td>
                            <td>{$courseNum[1]}</td>
                            <td class='non-print'>{$genButton}</td>
                            <td class='non-print' style = 'font-size:9pt; font-style:italic;'>{$courseNum[2]}</td>
                        </tr>";
            }
            $table .= "</tbody></table>";
        }else{
            $table = "No Student list.";
        }

        echo "{$table}";
        die();
    }

    // generate pdf.
    public function actionGenerateReportPdf(){//generate-report-pdf
        if (1) {
            $string = isset($_POST['string1']) && !empty($_POST['string1'])?(string)$_POST['string1']:die('Invalid request');
            $semregId = isset($_POST['semregId']) && !empty($_POST['semregId'])?intval($_POST['semregId']):die('Invalid request');
            try {
                $filename = $string ;
                $semregObject = SemesterRegistration2::findOne($semregId);

                $report = "";
                if ($semregObject) {
                    $handler = fopen('reports/'. $filename, 'w');

                    $report .= $this->getHeader($semregObject);

                    // getCourses Enrolled By the student with such semreg Entry Id.

                    $report .= $this->getEnrolledCourseAll($semregObject);
                }else{
                    $report = "Enrollement History not found.";
                }

                $cntnt = $this->renderPartial('student_srd_report',
                    [
                        'content'=>$report,
                        'file_name'=>$filename,
                    ]);

                $pdf = new Pdf([
                    // set to use core fonts only
                    'mode' => Pdf::MODE_UTF8,
                    // 'mode' => Pdf::MODE_CORE,
                    // A4 paper format
                    'format' => Pdf::FORMAT_A4,
                    // portrait orientation 
                    'orientation' => Pdf::ORIENT_PORTRAIT,
                    //'orientation' => Pdf::ORIENT_LANDSCAPE,
                    'cssFile' => '@vendor/kartik-v/yii2-mpdf/src/assets/kv-mpdf-bootstrap.css',
                    // any css to be embedded if required
                    'cssInline' => '.kv-heading-1{font-size:18px}',
                    // stream to browser inline
                    'destination' => Pdf::DEST_BROWSER,
                    // your html content input
                    'content' => $cntnt,
                    'options' => [
                    'title' => "{$filename} Semester regsistration report",
                    ],
                    'methods' => [
                        'SetHeader' => [' DEC Registrar System Students Semester registration Report ||Genereted On: ' . date("r")],
                    // 'SetFooter' => [Yii::$app->user->identity->firstName.'||Page {PAGENO}|'],
                     'SetFooter' => ['Excellence Through Achievment'],
                    ]
                ]);

                fwrite($handler, $pdf->render());
                fclose($handler);
                echo 1;
                die();

            } catch (\Exception $e) {
                throw new yii\web\NotFoundHttpException("{$e->getMessage()}", 1);
            }
        }else{
            throw new yii\web\NotFoundHttpException("Error Processing Request", 1);
            
        }
    }

    private function getHeader($semregObject){

        // get to which group did the stident was asigned in that semster and clslvl.
        $std = \backend\models\Student::findOne($semregObject->semreg_std_relId);
        $user = \common\models\User1::findOne($std->std_user_relId);

        $semregDetail = \frontend\models\SemesterRegistration2Detail::find()
            ->where(['srd2_semreg_relId'=>$semregObject->id])
            ->one();

        if ($semregDetail && count($semregDetail) > 0) {
            
        }else{
            return "No Course enrolled.";
        }
        $gset = \frontend\models\SemesterRegistration2Detail::find()
            ->where(['srd2_semreg_relId'=>$semregObject->id])
            ->one()->srd2_group_relId;

        $gset = \backend\models\WGroupSetup::findOne($gset);

        $dept = \backend\models\Department::findOne($std->std_dept_relId)->dept_name;
        $adm = \backend\models\Admission::findOne($std->std_admission_relId)->admission_name;
        $prog = \backend\models\Program::findOne($std->std_prog_relId)->program_name;
        $spec = is_null($gset->gs_spec_relId)?'-':\backend\models\Specialization::findOne($gset->gs_spec_relId)->spec_name;
        $clslvl = \backend\models\ClassLevel::findOne($gset->gs_clslvl_relId)->clslvl_num;

        $year = \backend\models\SchoolYear::findOne($gset->gs_acadYear_relId)->sch_year_desc;
        $sem = \backend\models\Semester::findOne($gset->gs_sem_relId)->semester_name;
        return "
            <h3><u>Semester registration Report</u></h3>

            Student Full Name : <u>{$user->first_name} {$user->middle_name} {$user->last_name}</u><br>

            Admission : <u>{$adm} {$prog}</u>
            Department : <u>{$dept}</u><br>
            Specialization : <u>{$spec}</u>
            Class Level : <u>{$clslvl}</u><br>

            Semester <i><u>{$sem}</u></i> of <i><u>{$year}</u></i>
        ";
    }

    private function getEnrolledCourseAll($semregObject){
        if ($semregObject) {
            # code...
        }else{
            return "Not Enrollement history found.";
        }
        $semregDetail = \frontend\models\SemesterRegistration2Detail::find()
            ->where(['srd2_semreg_relId'=>$semregObject->id])
            ->all();
        $report  = "";
        if ($semregDetail && count($semregDetail)) {

            $totalCrdt = 0;
            $report .= "<table class='table'>
                    <thead>
                        <th style='border:1px solid black;'>Course Code</th>
                        <th style='border:1px solid black;'>Course Title</th>
                        <th style='border:1px solid black;'>Crdt. Hr.</th>
                    </thead><tbody>
                    ";

            foreach ($semregDetail as $key) {
                $course = \backend\models\Course::findOne($key->srd2_course_relId);

                $totalCrdt += $course->course_crdthr;
                $report .= "<tr>
                                <td style='border:1px solid black;'>{$course->course_code}</td>
                                <td style='border:1px solid black;'>{$course->course_title}</td>
                                <td style='border:1px solid black; text-align:center;'>{$course->course_crdthr}</td>
                            </tr>";
            }

             $report .= "
                    <tr>
                        <td style='font-style:italic; border:1px solid black; text-align:center;' colspan=2>
                            <strong>Total Cedit Hour.</strong>
                        </td>
                        <td style='font-style:italic; border:1px solid black; text-align:center;'>
                            <strong><u>{$totalCrdt}</u></strong>
                        </td>
                    </tr>
                </tbody>
            </table>";
        return $report;
        }else{
            return "Enrollement history NOT found.";
        }
    }
    private function generateSemregReport($semregObject){
        // check if the file is geerated before.
        $acadYear = \backend\models\SchoolYear::findOne($semregObject->semreg_acadYear_relId);
        $sem = \backend\models\Semester::findOne($semregObject->semreg_semester_relId);
        $clslvl = \backend\models\ClassLevel::findOne($semregObject->semreg_clslvl_relId);

        $std = \backend\models\Student::findOne($semregObject->semreg_std_relId);
        $user = \common\models\User1::findOne($std->std_user_relId);
        $fileName = $user->first_name .'_'. $user->middle_name .'_'. $user->last_name[0] .'_'. str_replace('/', '_',$acadYear->sch_year_desc) . '_Sem_' . $sem->semester_name . '_YEAR_' . $clslvl->clslvl_num . '_semregReport.pdf';

        $filename = $fileName;
        try {
            $handler = fopen('reports/'. $filename , 'r');

            return [TRUE && (filesize('reports/'. $filename ) > 0), $filename, $semregObject->id];

        } catch (\Exception $e) {
            return [false, $filename, $semregObject->id];
        }
    }

    private function getEnrolledCourse($semregId,$stdId){
        $semregDetail = SemesterRegistration2Detail::find()
            ->where(['srd2_semreg_relId'=>$semregId])
            ->all();
            $semregDetailIds = [];
            if ($semregDetail && count($semregDetail) > 0) {
                foreach ($semregDetail as $key) {
                    $semregDetailIds[] = $key['id'];
                }
            }
        $ne = SemesterRegistration2Detail::find()
            ->where(['srd2_semreg_relId'=>$semregId])
            ->count();
        $adE = SemesterRegistration2Detail::find()
            ->where(['srd2_semreg_relId'=>$semregId,])
            ->andWhere(['IS NOT','srd2_coadd_flag',NULL])
            ->count();

        $pending = \backend\models\AddHistory::find()->where(['ah_srd2_relId' => $semregDetailIds,'ah_approved'=>NULL])->count();

        $pending = $pending == 0?(($semregDetail && count($semregDetail) > 0)?'Ready':'No Course Enrolled'):($pending.' Approval Pending Add requests.');
        return [$ne,$adE,$pending,count($semregDetail) > 0];
    }

}
