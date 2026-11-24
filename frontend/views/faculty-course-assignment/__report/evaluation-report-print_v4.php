<?php

/**
 * @var \backend\models\FacultyCourseAssignment $model
 */

use frontend\modules\AcStEva\models\AseCriteriaCategory;
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

    $criteriaCategory = AseCriteriaCategory::find()->orderBy('category_name')->all();
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
            <th>Competency</th>
            <th>Average Point</th>
            <th>Percentage</th>
            <th>Remark</th>
        </thead><tbody>";
    $unevaluated = 0;
    $criteriaAverage_sum=0;
    $percentage_sum=0;
    $count = 0;
    foreach($criteriaCategory as $criteria){
        // var_dump($model->instRel->instUserRel->user_type,json_decode($criteria->profess_list));exit;
        if(!in_array($model->instRel->instUserRel->user_type,json_decode($criteria->profess_list))){
            continue;
        }
        $criteriaAverage = $criteria->getAverageTotal($model,$acp,NULL);
        $percentage = '--';
        if($criteriaAverage == 0){
            $unevaluated++;
            $criteriaAverage = "--";
        }else{
            $percentage = sprintf('%0.2f', ($criteriaAverage * 100 / 5));
            $criteriaAverage_sum += $criteriaAverage;
            $percentage_sum += $percentage;
            $count++;
            $criteriaAverage = "{$criteriaAverage}/5.00";
        }
        echo "<tr>
                <td>{$criteria->category_name}</td>
                <td>{$criteriaAverage}</td>
                <td>{$percentage}</td>
                <td></td>
            </tr>";
    }

    if ($unevaluated == 0 && $count > 0) {
        $average_total = sprintf("%0.2f",$criteriaAverage_sum/$count);
        $average_percentage = sprintf("%0.2f",$percentage_sum/$count);
        echo "<tr>
                <td style='text-align: right; font-weight: bold;'>Arerage Total</td>
                <td style='text-align:center; text-decoration:underline;'>{$average_total}</td>
                <td style='text-align:center; text-decoration:underline;'>{$average_percentage}</td>
                <td></td>
            </tr>";
        $scale = getScale($average_total);
        if (is_numeric($average_total)) {
            echo "
                <tr>
                    <td colspan=4 style='text-align:center;'>{$scale}</td>
                </tr>";
        }
            $approvers = getApprovers($instructor->inst_user_relId);
            $app2 = isset($approvers[1][0]) ? $approvers[1][0] : "";
            $app1 = isset($approvers[0][0]) ? $approvers[0][0] : "";
            echo "
                <tr>
                 
                <td colspan='4' style='padding: 45px;'>
                    <table style='width: 90%; border: 0px; margin:auto;'>
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
    echo "<p class='bg-warning'>Invalid request</p>";
    return;
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