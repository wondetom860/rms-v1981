<?php

use \common\components\CommonControllerFunctions as CCF;
use yii\helpers\ArrayHelper;

$acadYearId;
$semId;
function __constructor()
{

    $cs = CCF::currentTime();
    $acadYearId = $cs['Y'];
    $semId = $cs['S'];
}

$coffDet = $model;
$course = \backend\models\Course::findOne(['id' => $coffDet->coff2det_course_relId]);
$instId = isset($ta) && !empty($ta) ? \backend\models\Instructor::findOne(['id' => $coffDet->coff2det_TA_relId]) : \backend\models\Instructor::findOne(['id' => $coffDet->coff2det_inst_relId]);
$user = \backend\models\User::findOne(['id' => $instId->inst_user_relId]);
$ReportTable = '';
__constructor();
$semester = $coffDet->coff2detCoff2Rel->coff2SemRel->semester_name;
$acadYear = $coffDet->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc;

// Check if the course is PG course:
$pgCourse = checkPGCourse($course->id);

$dept = getEvalueeDept(Yii::$app->user->identity->id);
$dept = $dept ? $dept : $coffDet->coff2detCourseRel->courseDeptRel->dept_name;
$dept = strtoupper($dept);

$instructor = \backend\models\Instructor::findOne(['inst_user_relId' => $user->id]);
$acr = \backend\models\AcademicRank::findOne(['id' => $instructor->inst_acr_relId]);
// $usr = \backend\models\User::findOne(['id'=>Yii::$app->user->identity->id]);
$img = '<img src="/uploads/user.png" class="user-image" height="180" wedth="160" alt="Evaluee Photo"/>';
$logoImg = '<img src="/uploads/' . CLIENT_LOGO . '" class="user-image" height="80" wedth="60" alt=""/>';
// <div class='col-sm-3' style='text-align:left;'>
//             {$img}
//         </div>

// <h2>". strtoupper(Yii::$app->params['client']) .", ". strtoupper(Yii::$app->params['college']) ."</h2>

$ReportTable = "<table class='table table-condensed table-sm' style='font-size:11pt;'>
                    <caption style='color:#000; font-weight:normal; text-align:center;'>
                        <u>DEPARTMENT OF {$dept}</u><br>
                        ACADEMIC EMPLOYEE EVALUATION REPORTING FORM
                        <p style='text-align:center;'> FOR <b><i>SEMESTER-{$semester}</i></b> OF <b><i>{$acadYear}</i></b> ACADEMIC YEAR</p>
                    </caption>
                    <tbody>
                        <tr>
                            <td style='font-weight:bold; text-align:right;'>Employee Name:</td>
                            <td colspan=2><u>{$instructor->getFullName()}</u></td>
                            <td style='font-weight:bold;'>Academic Rank:</td>
                            <td><u>{$acr->acr_name}</u></td>
                        </tr>
                        <tr>
                            <td style='font-weight:bold; text-align:right;'>Course Name:</td>
                            <td colspan=2><u>{$course->course_title}</u></td>
                            <td style='font-weight:bold;'>Course Code:</td>
                            <td><u>{$course->course_code}</u></td>
                        </tr>
                        <tr>
                            <td colspan=5 style='text-align: center;'><br>
                                <p class='bg-default' style='text-align:center; color:black; font-weight:bolder;'><u>EVALUATION RESULT</u></p>
                            </td>
                        </tr>";


// $ReportTable .= "<tbody>";
$pp = !is_null($pgCourse);
$studentEval = getStudentSemesterEvaluation($user->id, $course->id, $pp, $coffDet->coff2detCoff2Rel->coff2_acadYear_relId, $coffDet->coff2detCoff2Rel->coff2_sem_relId);
$peerEval = getPeerSemesterEvaluation($user->id, $course->id, $coffDet->coff2detCoff2Rel->coff2_acadYear_relId, $coffDet->coff2detCoff2Rel->coff2_sem_relId);
$imSupEval = getImmidiateSupervisorEvaluation($user->id, $course->id, $coffDet->coff2detCoff2Rel->coff2_acadYear_relId, $coffDet->coff2detCoff2Rel->coff2_sem_relId);
$pgEval = [];
//var_dump(!isset($imSupEval[3]));die();
$studentEvalpt = 'N/A';
$ses_max = 'N/A';
$type = NULL;
if (!is_null($studentEval) && is_array($studentEval)) {
    $studentEvalpt = sprintf("%0.2f", $studentEval[2]);
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
    $peerEvalpt = sprintf("%0.2f", $peerEval[2]);
    if (!is_null($peerEval[3])) {
        $pes_max = \backend\models\SemesterEvaluationTypes::findOne(['id' => $peerEval[3][0]])->semev_type_maxPoint;
    }
}

$imSupEvalpt = 'N/A';
$ies_max = 'N/A';
if (!is_null($imSupEval) && is_array($imSupEval)) {
    $imSupEvalpt = sprintf("%0.2f", $imSupEval[2]);
    if (is_array($imSupEval[3]) && !is_null($imSupEval[3][0])) {
        $ies_max = \backend\models\SemesterEvaluationTypes::findOne(['id' => $imSupEval[3][0]])->semev_type_maxPoint;
    }
    $imSupEvalpt = $imSupEvalpt > $ies_max ? $ies_max : $imSupEvalpt;
}

$pgEvalpt = 'N/A';
$pges_max = 'N/A';

//var_dump($pgCourse);die();

if (0 || $pgCourse) {

    //

    //PG instructor evaluation by PG instructor.
    // If so also include PG cordinator evaluation, 
    $pgEval = getPGCordinatorEvaluation(Yii::$app->user->identity->id, $course->id, $coffDet->coff2detCoff2Rel->coff2_acadYear_relId, $coffDet->coff2detCoff2Rel->coff2_sem_relId);
    //var_dump($pgEval);die();


    if (!is_null($pgEval) && is_array($pgEval)) {
        $pgEvalpt = round($pgEval[2], 2);
        if (!is_null($pgEval[3])) {
            $pges_max = \backend\models\SemesterEvaluationTypes::findOne(['id' => $pgEval[3][0]])->semev_type_maxPoint;
            $pges_max = sprintf("%0.2f", $pges_max);
        }
    }
} else {
    $pgEvalpt = 0;
}
$sum = calculateSum($studentEvalpt, $peerEvalpt, $imSupEvalpt, $pgEvalpt);

$sum = is_numeric($sum) ? sprintf("%0.2f", $sum) : $sum;
$pgEvalpt = is_numeric($pgEvalpt) ? sprintf("%0.2f", $pgEvalpt) : $pgEvalpt;
$imSupEvalpt = is_numeric($imSupEvalpt) ? sprintf("%0.2f", $imSupEvalpt) : $imSupEvalpt;
$pes_max = is_numeric($pes_max) ? sprintf("%0.2f", $pes_max) : $pes_max;
$ies_max = is_numeric($ies_max) ? sprintf("%0.2f", $ies_max) : $ies_max;
$ses_max = is_numeric($ses_max) ? sprintf("%0.2f", $ses_max) : $ses_max;


$ReportTable .= "<tr>
			<td style='text-align:center; text-align:right;' colspan=3>Student Evaluation:&nbsp;&nbsp;&nbsp;</td>
            <td colspan=2>({$studentEvalpt}/{$ses_max})</td>
		</tr>
		<tr>
			<td style='text-align:center; text-align:right;' colspan=3>Peer Evaluation:&nbsp;&nbsp;&nbsp;</td>
            <td colspan=2>({$peerEvalpt}/{$pes_max})</td>
		</tr>
		<tr>
			<td style='text-align:center; text-align:right;' colspan=3>Immediate Supervisor:&nbsp;&nbsp;&nbsp;</td>
            <td colspan=2>({$imSupEvalpt}/{$ies_max})</td>
		</tr>
		";
///var_dump($pgCourse && count($pgCourse) > 0);die();


// if ($pgCourse && count($pgCourse) > 0) {
//    $ReportTable .= "<tr>
//         <td style='text-align:center; text-align:right;' colspan=3>PG - Cordinator Evaluation: &nbsp;&nbsp;&nbsp;</td>
//         <td colspan=2>({$pgEvalpt}/{$pges_max})</td>
//     </tr>";
// }
$ReportTable .= "<tr style='font-weight:bold;'>
                    <td  style='font-weight:bold; text-align:center; text-align:right;' colspan=3>Average:&nbsp;&nbsp;&nbsp;&nbsp;</td>
                    <td  style='font-weight:bold; text-align:left; font-style: italic;' colspan=2> {$sum}/5.00 </td>
                </tr>";
$scale = getScale($sum);
// $approvers = getApprovers(Yii::$app->user->identity->id);
$approvers = getApprovers($user->id);

//var_dump($approvers);die();

// $ReportTable .= "</tbody><br><br><br>";
if (is_numeric($sum)) {
    $ReportTable .= "
                        <tr>
                            <td colspan=5 style='text-align:center;'>{$scale}</td>
                        </tr>";
}
$app2 = isset($approvers[1][0]) ? $approvers[1][0] : "";
$app1 = isset($approvers[0][0]) ? $approvers[0][0] : "";
$ReportTable .= "
                <tr>
                    <td colspan=5 style='text-align:center;'><br><br><br></td>
                </tr>
                <tr class=''>
                    <td colspan=3 style=' text-align:center;'>{$app1}</td>
                    <td colspan=2>{$app2}</td>
                </tr>
                <tr>
                    <td colspan=5 style='text-align:center;'><br></td>
                </tr>
                <tr>
                    <td colspan=3 style=' text-align:center;'> _______________</td>
                    <td colspan=2>_______________</td>
                </tr>
            </table>";

echo $ReportTable;

function checkPGCourse($courseId)
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

function getEvalueeDept($evalueeId)
{
    $evalueeRole = array_keys(Yii::$app->authManager->getRolesByUser($evalueeId))[0];

    if ($evalueeRole == 'Instructor' || $evalueeRole == 'DepartmentHead') {
        $instructor = \backend\models\Instructor::findOne(['inst_user_relId' => $evalueeId]);
        $dept = \backend\models\Department::findOne(['id' => $instructor->inst_dep_relId]);

        if ($dept) {
            return $dept->dept_name;
        }
        return NULL;
    } else {
        return NULL;
    }
}

function getApprovers($evalueeId)
{
    $imidiates1 = getImidietSuppervisor($evalueeId, true);

    //var_dump($imidiates1);die();
    $i1 = '';
    $name = $name2 = 'such user not registered yet.';

    $imidiates12 = [];
    if (is_array($imidiates1) && count($imidiates1) > 0) {
        if (!is_null($imidiates1[1])) {
            $name = $imidiates1[1]->first_name . ' ' . $imidiates1[1]->middle_name;
            $i1 = $imidiates1[1]->id;
            $imidiates12 = getImidietSuppervisor($i1, true);
            if (is_array($imidiates12) && count($imidiates12) > 0) {
                $name2 = is_null($imidiates12[1]) ? 'such user not registered yet.' : $imidiates12[1]->getFullName();
            }
            $imidiates12[1] = $name2;
        }
        $imidiates1[1] = $name;
    }

    return [$imidiates1, $imidiates12];
}
function getScale($sum)
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

function calculateSum($studentEval, $peerEval, $imSupEval, $pgEval = 0)
{
    // && is_numeric($pgEval)
    if (is_numeric($studentEval) && is_numeric($peerEval) && is_numeric($imSupEval)) {
        return ($studentEval + $peerEval + $imSupEval);
    } else {
        return 'Evaluation Not Completed.';
    }
}

function getISVEvalType($evaluatorId)
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
function getPGCordinatorEvaluation($evalueeId, $courseId, $acadYearId, $semId)
{

    $evalType = \backend\models\SemesterEvaluationTypes::findOne(['semev_type_level' => 13]);

    if ($evalType) {
        __constructor();
        // 'semev_course_relId'=>$courseId,
        $semev = \backend\models\SemesterEvaluation2::find()
            ->where([
                'semev_acadYear_relId' => $acadYearId,
                'semev_sem_relId' => $semId,
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

function getImmidiateSupervisorEvaluation($evalueeId, $courseId, $acadYearId, $semId)
{

    // Get immidate suppervisor for this $evaluee.
    $userId = getImidietSuppervisor($evalueeId);

    if (!($userId)) {
        return NULL;
    }


    // get Immidiate suppervisor's evaluation type.  ?Take This user type.

    $SV_evalType = getISVEvalType($userId->id);

    if (!($SV_evalType)) {
        return NULL;
    }
    // var_dump($SV_evalType);die();

    $totalEvaluators = 0;
    $evaluatedEvaluators = 0;
    $totalPoints = 0;
    $average = 0;

    __constructor();

    // 'semev_evalType_relId'=>$SV_evalType->id,
    if ($userId) {
        $semev = \backend\models\SemesterEvaluation2::find()
            ->where([
                'semev_acadYear_relId' => $acadYearId,
                'semev_sem_relId' => $semId,
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
        if ($semev) {
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

function getPeerSemesterEvaluation($evalueeId, $courseId, $acadYearId, $semId)
{
    __constructor();

    $evalType = \backend\models\SemesterEvaluationTypes::findOne(['semev_type_level' => [6]]);
    $semev = \backend\models\SemesterEvaluation2::find()
        ->where([
            'semev_acadYear_relId' => $acadYearId,
            'semev_sem_relId' => $semId,
            'semev_evalType_relId' => $evalType->id,
            'semev_evaluee_relId' => $evalueeId,
        ])
        ->all();

    $rr =  getResultArray($semev, $evalType);

    if (!is_null($rr)) {
        //$rr[2] *= $evalType->semev_type_maxPoint;
        $pt = $evalType->semev_type_maxPoint;
        $rr[2] = $rr[2] > $pt ? $pt : $rr[2];
        $rr[3][] = $evalType->id;
    }

    return $rr;
}

function getStudentSemesterEvaluation($evalueeId, $courseId, $pgId = false, $acadYearId, $semId)
{
    __constructor();
    $evalType = NULL;
    $types = [];
    $lvl = [];
    $mix = [];
    $pt = 0;

    if ($pgId) {
        $evalType = \backend\models\SemesterEvaluationTypes::find()->where(['semev_type_level' => [12]])->one();
        if ($evalType) {
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
            'semev_acadYear_relId' => $acadYearId,
            'semev_sem_relId' => $semId,
            'semev_evalType_relId' => $types,
            'semev_evaluee_relId' => $evalueeId,
            'semev_course_relId' => $courseId,
        ])
        ->andWhere(['semev2det_completed' => 1])
        ->all();

    $rr =  getResultArray($semev);

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

function getResultArray($model, $evalType = NULL)
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

function getImidietSuppervisor($evalueeId, $dept = false)
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
    if ($evalueeRole == 'Instructor' || $evalueeRole == 'Technical Assistant') {

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
