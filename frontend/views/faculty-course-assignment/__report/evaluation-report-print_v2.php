<?php

/**
 * @var \backend\models\FacultyCourseAssignment $model
 */

use frontend\modules\AcStEva\models\AseEvaluationControlSetting;
use yii\helpers\ArrayHelper;

if (isset($model)) {
    // $controlSettings = $model->getControlSettings()
    //     ->andFilterWhere([
    //         'OR',
    //         ['course_relId' => $model->coff2detRel->coff2det_course_relId],
    //         ['course_relId' => NULL]
    //     ])->all();
    $acp = $model->coff2detRel->coff2detAcProgRel;
    if(!$acp){
        $gset = $model->coff2detRel->coff2detCoff2Rel->coff2GroupRel;
        $acp = $gset->gsActiveProgRel;
    }
    $controlSettings = AseEvaluationControlSetting::find()
        ->innerJoinWith('deptPlanRel')
        ->innerJoin('ase_semester_evaluation', 'ase_semester_evaluation.id=ase_department_evaluation_schedule.header_relId')
        ->andWhere([
            'candidate_staff_relId' => $model->instRel->inst_user_relId,
            'academic_year_relId' => $acp->academic_year_relId,
        ])
        ->andWhere([
            'OR',
            ['course_relId' => $model->coff2detRel->coff2det_course_relId],
            ['course_relId' => NULL]
        ])
        ->all();

    if ($controlSettings) {
        $course = $model->coff2detRel->coff2detCourseRel;
        $dept = strtoupper($course->courseDeptRel->dept_name);
        $acy = $acp->academicYearRel;
        $instructor = $model->instRel;
        $acr = \backend\models\AcademicRank::findOne(['id' => $instructor->inst_acr_relId]);
        echo "<table class='table table-sm table-bordered table-condensed'>";
        echo "
            <caption style='color:#000; font-weight:normal; text-align:center;'>
                <u style='font-size: 10pt;'>DEPARTMENT OF <b>{$dept}</b></u><br>
                ACADEMIC EMPLOYEE EVALUATION REPORTING FORM
                <p style='text-align:center;'> FOR <b><i>SEMESTER-{$acy->semester}</i></b> OF <b><i>{$acy->school_year}</i></b> ACADEMIC YEAR</p>
                
                <div class='row' style='border-top: 1px solid black; padding-left: 28px; padding-top: 8px; font-size: 9pt;'>
                    <table style='width: 93%;'>
                        <tr>
                            <td style='text-align: right;'>Employee Name:</td>
                            <td style='text-align: left;'><b><u>{$instructor->getFullName()}</u></b></td>
                            <td style='text-align: right;'>Academic Rank:</td>
                            <td style='text-align: left;'><b><u>{$acr->acr_name}</u></b></td>
                        </tr>
                        <tr>
                            <td style='text-align: right;'>Course Title:</td>
                            <td style='text-align: left;'><b><u>{$course->course_title}</u></b></td>
                            <td style='text-align: right;'>Course Code:</td>
                            <td style='text-align: left;'><b><u>{$course->getCourseCredits()}</u></b></td>
                        </tr>
                    </table>
                </div>
            </caption>";
        echo "<thead style='background-color: #ACE;'>
                <th>Evaluation Entry</th>
                <th>Result</th>
                <th>Remark</th>
            </thead><tbody>";
        $total_average = 0;
        $total_obtained = 0;
        $unevaluated = 0;
        foreach ($controlSettings as $setting) {
            // check for role:
            // if($setting->candidate_staff_role != )
            $criteriaBundle = $setting->deptPlanRel->criteriaBundleRel;
            // $setting->deptPlanRel->criteriaBundleRel
            $evalutaionResults = $setting->aseEvaluationResults;
            if ($evalutaionResults) {
                foreach ($evalutaionResults as $evalresult) {
                    $bandle = $evalresult->controlSettingRel->deptPlanRel->criteriaBundleRel;
                    $scale = sprintf('%0.2f', $bandle->average_percentage);
                    $average_percentage = $evalresult->average_total * ($bandle->average_percentage / $bandle->average_point);
                    echo "<tr>
                        <td>{$bandle->code}({$bandle->evalTypeRel->type_name})</td>
                        <td>" . sprintf('%0.2f', $average_percentage) . "/{$scale}</td>
                        <td></td>
                    </tr>";
                    $total_average += $scale;
                    $total_obtained += $average_percentage;
                }
            } else {
                $unevaluated++;
                echo "<tr>
                    <td>{$criteriaBundle->code}({$criteriaBundle->evalTypeRel->type_name})</td>
                    <td><span class='bg-danger' style='font-style: italic;'>Evaluation result not found</span></td>
                    <td></td>
                </tr>";
            }
        }
        if ($unevaluated == 0 && ($total_average >= 100)) {
            $average_5 = sprintf('%0.2f', $total_obtained * 5 / $total_average);
            $total_obtained = sprintf('%0.2f', $total_obtained);
            echo "<tr style='font-weight: bold; background-color: #FD7;'>
                <td style='text-align: center;'>Total</td>
                <td style='text-align: center;'>{$total_obtained}/{$total_average} | {$average_5}/5.00</td>
                <td> </td>
            </tr>";

            $scale = getScale($average_5);
            if (is_numeric($average_5)) {
                echo "
                    <tr>
                        <td colspan=3 style='text-align:center;'>{$scale}</td>
                    </tr>";
            }
            $approvers = getApprovers($instructor->inst_user_relId);
            $app2 = isset($approvers[1][0]) ? $approvers[1][0] : "";
            $app1 = isset($approvers[0][0]) ? $approvers[0][0] : "";
            echo "
                <tr>
                 
                <td colspan=3' style='padding: 45px;'>
                    <table style='width: 90%; border: 0px;'>
                        <tr class=''>
                            <td style=' text-align:center;'>{$app1}</td>
                            <td >{$app2}</td>
                        </tr>
                        <tr>
                            <td colspan=2 style='text-align:center;'><br></td>
                        </tr>
                        <tr>
                            <td style=' text-align:center;padding: 10px;'> _______________</td>
                            <td style='padding: 10px;'>_______________</td>
                        </tr>
                    </table>                
                </td>
                
                </tr>
            ";
        }
        echo "</tbody></table>";
    } else {
        echo "<p class='bg-warning'>Evaluation not completed yet...</p>";
    }
} else {
    echo "<p class='bg-warning'>Invalid request</p>";
}

function getScale($sum)
{
    $scale = \backend\models\SemesterEvaluationScale::find()
        ->where(['>=', 'ses_maxPoint', $sum])
        ->andWhere(['<=', 'ses_minPoint', $sum])
        ->one();
    // $scale = \backend\models\SemesterEvaluationScale::find()
    //     ->where(" {$sum} BETWEEN ses_maxPoint AND ses_minPoint ")
    //     ->one();

    if ($scale) {
        if ($scale->ses_pass_status == 1) {
            return "We appreciate you for your <b><i>{$scale->ses_name}</i></b> performance.";
        }
        return "Your performance for this semester is {$scale->ses_name}";
    }
    return 'N/A';
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
