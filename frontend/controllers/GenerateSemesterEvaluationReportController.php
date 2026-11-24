<?php

namespace frontend\controllers;

use yii;
use yii\helpers\ArrayHelper;
use \backend\models\CourseOffering2DetailSearch;
use \common\components\CommonControllerFunctions as CCF;
use yii\filters\VerbFilter;

class GenerateSemesterEvaluationReportController extends \yii\web\Controller
{

    public $acadYearId;
    public $semId;
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
                        // Allow  actions for loged in users only
                        'roles' => ['@',],
                    ],
                ]
            ]
        ];
    }

    protected function __constructor()
    {

        $cs = CCF::currentTime();
        $this->acadYearId = $cs['Y'];
        $this->semId = $cs['S'];
    }


    public function actionIndexReportByOffering() //index-report-by-offering
    {
        $searchModel = new CourseOffering2DetailSearch();
        $dataProvider = $searchModel->search(Yii::$app->request->queryParams);
        return $this->render('index2', ['searchModel' => $searchModel, 'dataProvider' => $dataProvider]);
    }

    public function actionIndex()
    {
        return $this->render('index');
    }

    protected function getEvalueeDept($evalueeId)
    {
        $evalueeRole = array_keys(Yii::$app->authManager->getRolesByUser($evalueeId))[0];

        if ($evalueeRole == 'Instructor' || $evalueeRole == 'DepartmentHead') {
            $instructor = \backend\models\Instructor::findOne(['inst_user_relId' => $evalueeId]);
            $dept = \backend\models\Department::findOne(['id' => $instructor->inst_dep_relId]);

            if ($dept) {
                return $dept->dept_name;
            }
            return 'NOT YET KNOWN';
        } else {
            return 'NOT YET KNOWN';
        }
    }

    // return the list of courses assigned to an instructor as a dropdown list.
    public function actionGetInstCourse()
    {

        if (Yii::$app->user->can('DepartmentHead')) {
        } else {
            die("<p class-'bg-danger'>You Can\'t print Semester Evaluation Report.</p>");
        }

        $instId = '';
        $select = "<select id='selectInstCourse' onchange='loadEvaluationReport($(this).val())'>";
        // if (isset($_POST['instId']) && !empty($_POST['instId'])) {
        //     $instId = $_POST['instId'];
        // }else{
        $instId = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
        if (!empty($instId)) {
            $instDept = $instId->inst_dep_relId;
        } else {
            die('User Account Error');
        }

        $this->__constructor();

        $coff2det = \backend\models\CourseOffering2Detail::find()
            ->innerJoin('course_offering2', 'course_offering2.id = course_offering2_detail.coff2det_coff2_relId')
            ->innerJoin('course', 'course.id = course_offering2_detail.coff2det_course_relId')
            ->where([
                'course_offering2.coff2_acadYear_relId' => $this->acadYearId,
                'course_offering2.coff2_sem_relId' => $this->semId,
                'course.course_dept_relId' => $instDept,
            ])
            ->groupBy('coff2det_course_relId')
            ->all();

        $select .= "<option value=''> - </option>";

        if ($coff2det && count($coff2det) > 0) {
            foreach ($coff2det as $coff2det2) {

                $coff2det3 = NULL;

                if (is_null($coff2det2->coff2det_inst_relId)) {
                    continue;
                }

                // $coff2det3 = \backend\models\CourseOffering2Detail::find()
                //     ->where([
                //         'coff2det_coff2_relId'=>$coff2det2['coff2det_coff2_relId'],
                //         'coff2det_course_relId'=>$coff2det2['coff2det_course_relId']
                //     ])
                //     ->andWhere(['OR',
                //         ['coff2det_inst_relId'=>$coff2det2['coff2det_inst_relId'],],
                //         ['coff2det_TA_relId'=>$coff2det2['coff2det_TA_relId'],]])
                //     ->all();
                $coff2det3 = \backend\models\CourseOffering2Detail::find()
                    ->innerJoin('course_offering2', 'course_offering2.id=course_offering2_detail.coff2det_coff2_relId')
                    ->where([
                        'coff2_acadYear_relId' => $coff2det2->coff2detCoff2Rel->coff2_acadYear_relId,
                        'coff2_sem_relId' => $coff2det2->coff2detCoff2Rel->coff2_sem_relId,
                        'coff2det_course_relId' => $coff2det2->coff2det_course_relId
                    ])
                    ->all();

                $gset = NULL;
                $gname = '[';
                foreach ($coff2det3 as $key) {
                    $gsetId = \backend\models\CourseOffering2::findOne(['id' => $coff2det2['coff2det_coff2_relId']])->coff2_group_relId;
                    $gset = \backend\models\WGroupSetup::findOne(['id' => $gsetId]);
                    $gname .= \backend\models\WGroup::findOne(['id' => $gset->gs_group_relId])->group_name . ',';
                }
                $gname .= ']';
                $admission = \backend\models\Admission::findOne(['id' => $gset->gs_adm_relId])->admission_name;
                $program = \backend\models\Program::findOne(['id' => $gset->gs_prog_relId])->program_name;
                $department = \backend\models\Department::findOne(['id' => $gset->gs_dept_relId])->dept_name;
                $clslvl = \backend\models\ClassLevel::findOne(['id' => $gset->gs_clslvl_relId])->clslvl_num;
                $inst = \backend\models\Instructor::findOne(['id' => $coff2det2['coff2det_inst_relId']]);
                $instUser = \backend\models\User::findOne(['id' => $inst->inst_user_relId]);
                $course = \backend\models\Course::findOne(['id' => $coff2det2['coff2det_course_relId']]);
                $title = $admission . '_' . $program . '_' . $department . '_' . $clslvl . '_ ' . count($coff2det3) . 'Classes.';
                $name = $instUser->first_name . ' ' . $instUser->middle_name . ' [' . $course->course_title . '] -> ' . $gname;
                $select .= "<option value='" . $coff2det2['id'] . "' title='{$title}'> " . $name . " </option>";
            }
            $select .= "</select>";

            return $select;
        }
    }

    public function actionGetReport()
    { //get-report 

        $coffDetId = isset($_POST['coffDetId']) && !empty($_POST['coffDetId']) ? $_POST['coffDetId'] : (isset($_GET['coffDetId']) && !empty($_GET['coffDetId']) ? $_GET['coffDetId'] : die('Uknown course'));
        $ta = isset($_GET['ta']) && !empty($_GET['ta']) ? $_GET['ta'] : null;

        $coffDet = \backend\models\CourseOffering2Detail::findOne(['id' => intval($coffDetId)]);
        $_SESSION['report'] = str_replace([' ', '/'], '_', "AcStEv_" . ($ta ? $coffDet->coff2detTARel->getFullName() : $coffDet->coff2detInstRel->getFullName()) . "_" . $coffDet->coff2detCourseRel->getCourseCredits());
        if (isset($_GET['__loyout_print']) && !empty($_GET['__loyout_print'])) {
            $this->layout = 'evaluationReportPrintLayout';
            return $this->render('evaluation-report-print', ['model' => $coffDet, 'ta' => $ta]);
        }

        $coffDet = \backend\models\CourseOffering2Detail::findOne(['id' => intval($coffDetId)]);
        $course = \backend\models\Course::findOne(['id' => $coffDet->coff2det_course_relId]);
        $instId = \backend\models\Instructor::findOne(['id' => $coffDet->coff2det_inst_relId]);
        $user = \backend\models\User::findOne(['id' => $instId->inst_user_relId]);
        $ReportTable = '';
        $this->__constructor();
        $semester = \backend\models\Semester::findOne(['id' => $this->semId])->semester_name;
        $acadYear = Yii::$app->params['C']['Y'];

        // Check if the course is PG course:
        $pgCourse = $this->checkPGCourse($course->id);

        $dept = $this->getEvalueeDept(Yii::$app->user->identity->id);
        $dept = strtoupper($dept);

        $instructor = \backend\models\Instructor::findOne(['inst_user_relId' => $user->id]);
        $acr = \backend\models\AcademicRank::findOne(['id' => $instructor->inst_acr_relId]);
        // $usr = \backend\models\User::findOne(['id'=>Yii::$app->user->identity->id]);
        $img = '<img src="/uploads/user.png" class="user-image" height="180" wedth="160" alt="Evaluee Photo"/>';
        $logoImg = '<img src="/uploads/' . CLIENT_LOGO . '" class="user-image" height="80" wedth="60" alt=""/>';
        // <div class='col-sm-3' style='text-align:left;'>
        //             {$img}
        //         </div>
        $ReportTable .= "<br><br>
            <div class='container-fluid'>
                <div class='col-md-12' style='text-align:center;'>{$logoImg}</div>
                <div class='col-md-12' style='text-align:center;'>
                    <h2>" . strtoupper(Yii::$app->params['client']) . ", " . strtoupper(Yii::$app->params['college']) . "</h2>
                    <u>DEPARTMENT OF {$dept}</u><br>
                    ACADEMIC EMPLOYEE EVALUATION REPORTING FORM
                </div>
            </div>
            <div class='container-fluid' style='border-top:2px solid black; padding:0px 90px;'>
            <div class='col-md-12'><br>
            <p style='text-align:center;'> FOR <b><i>SEMESTER-{$semester}</i></b> OF <b><i>{$acadYear}</i></b> ACADEMIC YEAR</p>

        ";

        $ReportTable .= "<table class='table table-striped table-condensed table-sm' style='margin-top:-15px;'>";
        $ReportTable .= "<caption style='color:black;'>
                    
                    <table class='table' style='border:1px solid black;'>
                    <tbody>
                        <tr><td>Name : <u>{$user->first_name} {$user->middle_name}</u></td><td rowspan=4><div style='text-align:right;'>{$img}</div></td></tr>
                        <tr><td>Academic Rank :<u>{$acr->acr_name}</u></td></tr>
                        <tr><td>Course Name :<u>{$course->course_title}</u></td></tr>
                        <tr><td>Course Code : <u>{$course->course_code}</u></td></tr>
                        
                    </tbody>
                    </table>
                    <p class='bg-default' style='text-align:center; color:black; font-weight:bolder;'><u>EVALUATION RESULTS</u></p>
                    </caption>";
        $ReportTable .= "<tbody>";
        $pp = !is_null($pgCourse);
        $studentEval = $this->getStudentSemesterEvaluation($user->id, $course->id, $pp);
        $peerEval = $this->getPeerSemesterEvaluation($user->id, $course->id);
        $imSupEval = $this->getImmidiateSupervisorEvaluation($user->id, $course->id);
        $pgEval = [];
        //var_dump(!isset($imSupEval[3]));die();
        $studentEvalpt = 'N/A';
        $ses_max = 'N/A';
        $type = NULL;
        if (!is_null($studentEval) && is_array($studentEval)) {
            $studentEvalpt = round($studentEval[2], 2);
            if (!is_null($studentEval[3])) {
                $ses = \backend\models\SemesterEvaluationTypes::findOne(['id' => $studentEval[3][0]]);
                $type = $ses->semev_type_level;
                $ses_max = $ses->semev_type_maxPoint;
                // ->   // 
            }
        }
        $peerEvalpt = 'N/A';
        $pes_max = 'N/A';
        if (!is_null($peerEval) && is_array($peerEval)) {
            $peerEvalpt = round($peerEval[2], 2);
            if (!is_null($peerEval[3])) {
                $pes_max = \backend\models\SemesterEvaluationTypes::findOne(['id' => $peerEval[3][0]])->semev_type_maxPoint;
            }
        }

        $imSupEvalpt = 'N/A';
        $ies_max = 'N/A';
        if (!is_null($imSupEval) && is_array($imSupEval)) {
            // $imSupEvalpt
            $imSupEvalpt = round($imSupEval[2], 2);
            if (is_array($imSupEval[3]) && !is_null($imSupEval[3][0])) {
                $ies_max = \backend\models\SemesterEvaluationTypes::findOne(['id' => $imSupEval[3][0]])->semev_type_maxPoint;
            }
            $imSupEvalpt = $imSupEvalpt > $ies_max ? $ies_max : $imSupEvalpt;
        }

        $pgEvalpt = 'N/A';
        $pges_max = 'N/A';

        //var_dump($pgCourse);die();

        if ($pgCourse && count($pgCourse) > 0) {

            //

            //PG instructor evaluation by PG instructor.
            // If so also include PG cordinator evaluation, 
            $pgEval = $this->getPGCordinatorEvaluation(Yii::$app->user->identity->id, $course->id);
            //var_dump($pgEval);die();


            if (!is_null($pgEval) && is_array($pgEval)) {
                $pgEvalpt = round($pgEval[2], 2);
                if (!is_null($pgEval[3])) {
                    $pges_max = \backend\models\SemesterEvaluationTypes::findOne(['id' => $pgEval[3][0]])->semev_type_maxPoint;
                }
            }
        } else {
            $pgEvalpt = 0;
        }
        $sum = $this->calculateSum($studentEvalpt, $peerEvalpt, $imSupEvalpt, $pgEvalpt);


        $ReportTable .= "<tr>
				<td style='text-align:center;'>Student Evaluation</td>
				<td>({$studentEvalpt}/{$ses_max})</td>
			</tr>
			<tr>
				<td style='text-align:center;'>Peer Evaluation</td>
				<td>({$peerEvalpt}/{$pes_max})</td>
			</tr>
			<tr>
				<td style='text-align:center;'>Immediate Supervisor</td>
				<td>({$imSupEvalpt}/{$ies_max})</td>
			</tr>
			";
        ///var_dump($pgCourse && count($pgCourse) > 0);die();


        if (0 || $pgCourse && count($pgCourse) > 0) {
            $ReportTable .= "<tr>
                    <td style='text-align:center;'>PG - Cordinator Evaluation</td>
                    <td>({$pgEvalpt}/{$pges_max})</td>
                </tr>";
        }
        $ReportTable .= "<tr style='background-color:#ace;'>
                <td  style='font-weight:bold; text-align:center'>Average</td>
                <td  style='font-weight:bold; text-align:left; font-style: italic;'> ({$sum})/5.00 </td>
            </tr>";
        $scale = $this->getScale($sum);
        $approvers = $this->getApprovers(Yii::$app->user->identity->id);

        //var_dump($approvers);die();

        $pp = isset($approvers[0][0]) ? $approvers[0][0] : '';
        $pp2 = isset($approvers[1][0]) ? $approvers[1][0] : '';
        $ReportTable .= "</tbody><br><br><br>";
        if (is_numeric($sum)) {
            $ReportTable .= "
                    <tfoot><tr>
                        <td colspan=2 style='text-align:center;'>{$scale}</td></tr>
                    </tfoot>";
        }
        $ReportTable .= "
                    </table></div>
                    <div class='col-md-2'> </div>
            </div>
            <div class='container-fluid' style='text-align:center; border-top:2px solid black;'>
                <table class='table table-condensed table-sm'><tr>
                    <td>
                        <div class='container-fluid'>
                        <b class='hidden'>{$pp}</b>
                        <br><br><br>
                         _______________
                        <br><br><br>
                        </div>
                    </td>
                    <td>
                        <div class='container-fluid'>Seal</div>
                    </td>
                    <td>
                        <div class='container-fluid' style='text-align:left;'>
                        <b class='hidden'>{$pp2}</b>
                        <br><br><br>
                         _______________
                        <br><br><br>
                        </div>
                    </td>
                </tr></table>
                </div>
            </div>
        ";

        return $ReportTable;
    }

    protected function checkPGCourse($courseId)
    {
        $pgProgram = \backend\models\Program::findOne(['program_name' => 'MASTERS']);

        $pgCourse = \backend\models\CourseCatalog::find()
            ->where([
                'cocat_course_relId' => $courseId,
                'cocat_program_relId' => $pgProgram->id,
            ])
            ->one();

        return $pgCourse;
    }

    protected function getApprovers($evalueeId)
    {
        $imidiates1 = $this->getImidietSuppervisor($evalueeId, true);

        //var_dump($imidiates1);die();
        $i1 = '';
        $name = $name2 = 'such user not registered yet.';

        $imidiates12 = [];
        if (is_array($imidiates1) && count($imidiates1) > 0) {
            if (!is_null($imidiates1[1])) {
                $name = $imidiates1[1]->first_name . ' ' . $imidiates1[1]->middle_name;
                $i1 = $imidiates1[1]->id;
                $imidiates12 = $this->getImidietSuppervisor($i1, true);
                if (is_array($imidiates12) && count($imidiates12) > 0) {
                    $name2 = is_null($imidiates12[1]) ? 'such user not registered yet.' : $imidiates12[1]->first_name . ' ' . $imidiates12[1]->middle_name;
                }
                $imidiates12[1] = $name2;
            }
            $imidiates1[1] = $name;
        }

        return [$imidiates1, $imidiates12];
    }
    protected function getScale($sum)
    {
        $scale = \backend\models\SemesterEvaluationScale::find()->where([
            '>=',
            'ses_maxPoint',
            $sum,
        ])->andWhere(['<=', 'ses_minPoint', $sum])->one();

        if ($scale) {
            if ($scale->ses_pass_status == 1) {
                return "We appreciate you for your <b><i>{$scale->ses_name}</i></b> performance.";
            }
            return "Your performance for this semester is {$scale->ses_name}";
        }
        return 'N/A';
    }

    protected function calculateSum($studentEval, $peerEval, $imSupEval, $pgEval = 0)
    {
        //  && is_numeric($pgEval)
        if (is_numeric($studentEval) && is_numeric($peerEval) && is_numeric($imSupEval)) {
            return ($studentEval + $peerEval + $imSupEval);
        } else {
            return 'Evaluation Not Completed.';
        }
    }

    protected function getISVEvalType($evaluatorId)
    {
        // show user role.
        $ISV_ROLE = array_keys(Yii::$app->authManager->getRolesByUser($evaluatorId))[0];
        $semev_type_level = '';
        if ($ISV_ROLE == 'DepartmentHead') {
            $semev_type_level = 8; //suppervise instructors by HoD
        } else if ($ISV_ROLE == 'AcademicDean') { //Suppervise HoDs by ACADEMIC_DEAN
            $semev_type_level = 11;
        } else if ($ISV_ROLE == 'Commandant') { //Suppervise Deans by Commandant
            $semev_type_level = 10;
        } else if ($ISV_ROLE == 'PGCoordinator') { //Evaluate PG Instructors -By PG Coordinator
            $semev_type_level = 13;
        }

        $evalType = \backend\models\SemesterEvaluationTypes::findOne(['semev_type_level' => $semev_type_level]);
        return $evalType;
    }
    // 
    protected function getPGCordinatorEvaluation($evalueeId, $courseId)
    {

        $evalType = \backend\models\SemesterEvaluationTypes::findOne(['semev_type_level' => 13]);

        if ($evalType) {
            $this->__constructor();
            // 'semev_course_relId'=>$courseId,
            $semev = \backend\models\SemesterEvaluation2::find()
                ->where([
                    'semev_acadYear_relId' => $this->acadYearId,
                    'semev_sem_relId' => $this->semId,
                    'semev_evaluee_relId' => $evalueeId,
                    'semev_evalType_relId' => $evalType->id,
                ])->innerJoin(
                    'semester_evaluation2_detail',
                    'semester_evaluation2.id = semester_evaluation2_detail.semev2det_semev_relId'
                )
                ->andWhere([
                    'semester_evaluation2_detail.semev2det_completed' => 1,
                ])
                ->One();
            if ($semev && count($semev) > 0) {
                $totalEvaluators = 1;
                $evaluatedEvaluators = 1;
                $totalPoints = $semev->semev_averagePoint;
                $average = $totalPoints; // * $evalType->semev_type_maxPoint;
                $ggg[] = $evalType->id;
                return [1, $totalPoints, $average, $ggg];
            } else {
                return NULL;
            }
        } else {
            return NULL;
        }
    }

    protected function getImmidiateSupervisorEvaluation($evalueeId, $courseId)
    {

        // Get immidate suppervisor for this $evaluee.
        $userId = $this->getImidietSuppervisor($evalueeId);

        if (!($userId)) {
            return NULL;
        }


        // get Immidiate suppervisor's evaluation type.  ?Take This user type.

        $SV_evalType = $this->getISVEvalType($userId->id);

        if (!($SV_evalType)) {
            return NULL;
        }
        // var_dump($SV_evalType);die();

        $totalEvaluators = 0;
        $evaluatedEvaluators = 0;
        $totalPoints = 0;
        $average = 0;

        $this->__constructor();

        // 'semev_evalType_relId'=>$SV_evalType->id,
        if ($userId) {
            $semev = \backend\models\SemesterEvaluation2::find()
                ->where([
                    'semev_acadYear_relId' => $this->acadYearId,
                    'semev_sem_relId' => $this->semId,
                    'semev_evaluee_relId' => $evalueeId,
                    'semev_evalType_relId' => $SV_evalType->id,
                ])->innerJoin(
                    'semester_evaluation2_detail',
                    'semester_evaluation2.id = semester_evaluation2_detail.semev2det_semev_relId'
                )
                ->andWhere([
                    'semester_evaluation2_detail.semev2det_completed' => 1,
                    'semester_evaluation2_detail.semev2det_evaluator_relId' => $userId,
                ])
                ->One();
            if ($semev && count($semev) > 0) {
                $totalEvaluators = 1;
                $evaluatedEvaluators = 1;
                $totalPoints = $semev->semev_averagePoint;
                $average = $totalPoints; // * $SV_evalType->semev_type_maxPoint;
                $ggg[] = $SV_evalType->id;
                return [1, $totalPoints, $average, $ggg];
            } else {
                return NULL;
            }
        } else {
            return NULL;
        }
    }

    protected function getPeerSemesterEvaluation($evalueeId, $courseId)
    {
        $this->__constructor();

        $evalType = \backend\models\SemesterEvaluationTypes::findOne(['semev_type_level' => [6]]);
        $semev = \backend\models\SemesterEvaluation2::find()
            ->where([
                'semev_acadYear_relId' => $this->acadYearId,
                'semev_sem_relId' => $this->semId,
                'semev_evalType_relId' => $evalType->id,
                'semev_evaluee_relId' => $evalueeId,
            ])
            ->all();

        $rr =  $this->getResultArray($semev, $evalType);

        if (!is_null($rr)) {
            //$rr[2] *= $evalType->semev_type_maxPoint;
            $pt = $evalType->semev_type_maxPoint;
            $rr[2] = $rr[2] > $pt ? $pt : $rr[2];
            $rr[3][] = $evalType->id;
        }

        return $rr;
    }

    protected function getStudentSemesterEvaluation($evalueeId, $courseId, $pgId = false)
    {
        $this->__constructor();
        $evalType = NULL;
        $types = [];
        $lvl = [];
        $mix = [];
        $pt = 0;

        if ($pgId) {
            $evalType = \backend\models\SemesterEvaluationTypes::find()->where(['semev_type_level' => [12]])->one();
            if ($evalType && count($evalType) > 0) {
                $types[] = $evalType->id;
                $mix[$evalType->semev_type_level] = $evalType->id;
                $lvl[] = $evalType->semev_type_level;
                $pt = $evalType->semev_type_maxPoint;
            } else {
                return NULL;
            }
        } else {
            $evalType = \backend\models\SemesterEvaluationTypes::find()->where(['semev_type_level' => [4, 5]])->all();
            // var_dump($evalType);die();
            if ($evalType && count($evalType) > 0) {
                foreach ($evalType as $key) {
                    $types[] = $key['id'];
                    $lvl[] = $key['semev_type_level'];
                    $mix[$key['semev_type_level']] = $key['id'];
                    $pt = $key['semev_type_maxPoint'];
                }
            }
        }
        //var_dump($evalType);die();
        $semev = \backend\models\SemesterEvaluation2::find()
            ->innerJoin('semester_evaluation2_detail', 'semev2det_semev_relId = semester_evaluation2.id')
            ->where([
                'semev_acadYear_relId' => $this->acadYearId,
                'semev_sem_relId' => $this->semId,
                'semev_evalType_relId' => $types,
                'semev_evaluee_relId' => $evalueeId,
                'semev_course_relId' => $courseId,
            ])
            ->andWhere(['semev2det_completed' => 1])
            ->all();

        $rr =  $this->getResultArray($semev);

        if (!is_null($rr)) {
            // for students evaluation case the average point is out if 5.0
            // down scale it to $evalType->maxPoint
            if (in_array(12, $lvl)) { // PG case
                $rr[3][] = $types;
            } else {
                $rr[3][] = $types;
            }
            $rr[2] *= ($pt);
            $rr[2] = $rr[2] > $pt ? $pt : $rr[2];
        }

        // var_dump($rr);die();
        return $rr;
    }

    protected function getResultArray($model, $evalType = NULL)
    {
        $totalEvaluators = 0;
        $evaluatedEvaluators = 0;
        $totalPoints = 0;
        $average = 0;
        $evalType =  NULL;

        // var_dump($evalType);die();

        if ($model && count($model) > 0) {
            foreach ($model as $value) {
                $exe = \backend\models\SemesterEvaluationReportController::getReport($value['id']);
                $totalEvaluators += count($exe[1]);
                $evaluatedEvaluators += count($exe[0]);
                $totalPoints += ($value['semev_averagePoint']);
            }
            if ($evaluatedEvaluators > 0 && $totalEvaluators > 0) {

                if (($evaluatedEvaluators / $totalEvaluators) > 0.2) {
                    $average = $totalPoints / $evaluatedEvaluators;

                    //$average *= 2.5;
                    // $average = ($average/100) * $value
                    return [$evaluatedEvaluators, $totalPoints, $average, $evalType, $totalEvaluators];
                } else if ($evalType && ($evalType->semev_type_level == 6)) {
                    $average = $totalPoints / $evaluatedEvaluators;
                    return [$evaluatedEvaluators, $totalPoints, $average, $evalType, $totalEvaluators];
                } else {
                    return NULL;
                }
            } else {
                return NULL;
            }
        } else {
            return NULL;
        }
    }
    protected function getImidietSuppervisor($evalueeId, $dept = false)
    {
        // print_r();die();
        //$evalueeRole = array_keys(Yii::$app->authManager->getRolesByUser($evalueeId));
        $nn = Yii::$app->authManager->getRolesByUser($evalueeId);
        //$evalueeRole =   ;
        $evalueeRole =  array_keys($nn);
        //print_r($evalueeRole[0]) ; die();
        if (is_array($evalueeRole) && count($evalueeRole) > 0) {
            $evalueeRole = ArrayHelper::getValue($evalueeRole, 0);
            // /echo $evalueeRole;
        }
        if ($evalueeRole == 'Instructor') {

            // Get his dept.
            $instructor = \backend\models\Instructor::findOne(['inst_user_relId' => $evalueeId]);

            // Get HoD of his dept.

            $dh = \backend\models\DepartmentHead::findOne([
                'dh_dept_relId' => $instructor->inst_dep_relId,
                'dh_status' => 1
            ]);

            if ($dh) {
                //var_dump($dh);die();
                $userId = \backend\models\Instructor::findOne(['id' => $dh->dh_inst_relId])->inst_user_relId;
                $rolleee  = \backend\models\User::findOne(['id' => $userId]);
                if ($dept) { // cal wants to get role Name also.
                    return ['Department Head', $rolleee];
                }
                return $rolleee;
            }
            // He / she is $evaluee's Imidiet evaluator
            die('Your Department Head not known.');
        } else if ($evalueeRole  == 'DepartmentHead') {
            // Get Academic Dean
            $acIds = Yii::$app->authManager->getUserIdsByRole('AcademicDean');
            $rolleee  = \backend\models\User::findOne(['id' => $acIds]);
            if ($dept) { // cal wants to get role Name also.
                return ['Academic Dean', $rolleee];
            }
            return $rolleee;
            // He /she is his/ her imidiate suppervisor
        } else if ($evalueeRole  == 'AcademicDean') {
            // Get Commandant
            $acIds = Yii::$app->authManager->getUserIdsByRole('Commandant');
            $rolleee  = \backend\models\User::findOne(['id' => $acIds]);
            if ($dept) { // cal wants to get role Name also.
                return ['College Commandant', $rolleee];
            }
            return $rolleee;
        }
    }
}
