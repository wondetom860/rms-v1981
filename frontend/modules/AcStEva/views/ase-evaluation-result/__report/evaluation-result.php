<?php

/**
 * @var \frontend\modules\AcStEva\models\AseEvaluationresult $model
 */

use frontend\modules\AcStEva\models\AseEvaluationControlSetting;

if (isset($model)) {
    $controlSettings = AseEvaluationControlSetting::find()
        ->innerJoinWith('deptPlanRel')
        ->where([
            'candidate_staff_relId' => $model->controlSettingRel->candidate_staff_relId,
            'header_relId' => $model->deptPlanRel->header_relId
        ])
        ->all();
    if ($controlSettings) {
        echo "<table class='table table-sm table-bordered table-condensed' style='font-size: 9pt;'>";
        echo "<thead style='background-color: #ACE;'>
                <th>#</th>
                <th>Evaluation Type</th>
                <th>Evaluators</th>
                <th>Course</th>
                <th>Avrg. Rate</th>
                <th>Percentage</th>
                <th></th>
            </thead><tbody>";
        $count = 0;
        foreach ($controlSettings as $controlSetting) {
            $evalutaionResults = $controlSetting->aseEvaluationResults;
            if ($evalutaionResults) {
                foreach ($evalutaionResults as $evalresult) {
                    $evalresult->computeAverageLast();
                    $scale = sprintf('%0.2f', $evalresult->criteriaBundleRel->average_percentage);
                    $rate = sprintf('%0.2f', $evalresult->criteriaBundleRel->average_point);
                    $rated = sprintf('%0.2f', $evalresult->average_total);
                    $evs = $evalresult->completed_staffs ? count(json_decode($evalresult->completed_staffs, true)) : 0;
                    $average_percentage = ($evalresult->average_total * ($evalresult->criteriaBundleRel->average_percentage / $evalresult->criteriaBundleRel->average_point));
                    $course = $evalresult->criteriaBundleRel->evaluate_per_course ? $evalresult->controlSettingRel->courseRel->getCourseCredits() : '-';
                    $totalEvaltrs = count(json_decode($evalresult->controlSettingRel->evaluators));
                    $evalsPercent = sprintf('%0.2f', ($evs / $totalEvaltrs) * 100);
                    $printBtn = (Yii::$app->user->can('DepartmentHead') && $evalresult->criteriaBundleRel->evaluate_per_course) ?
                        ($evalsPercent > 51 ? "
                        <button class='btn btn-xs btn-link pull-right' title='Print Evaluation Result' onclick='PrintEvaluationResult({$controlSetting->faculty_assignment_relId});return false;'>Print</button>
                        <button class='btn btn-xs btn-link pull-right d-none hidden' title='Print Evaluation Result2' onclick='PrintEvaluationResult3({$controlSetting->faculty_assignment_relId});return false;'>Print2</button>
                        " : "<span class='label bg-primary pull-right'>{$evalsPercent}%</span>") : '';
                    if ($evalresult->average_percentage != $average_percentage) {
                        $evalresult->average_percentage = $average_percentage;
                        $evalresult->update(false);
                    }
                    echo "<tr>
                        <td>" . ++$count . "</td>
                        <td>{$evalresult->criteriaBundleRel->code}({$evalresult->criteriaBundleRel->evalTypeRel->type_name})</td>
                        <td>{$evs}/{$totalEvaltrs}{$printBtn}</td>
                        <td>{$course}</td>
                        <td>{$rated}/{$rate}</td>
                        <td>" . sprintf('%0.2f', $average_percentage) . "/{$scale}</td>
                        <td></td>
                    </tr>";
                }
            }
        }
        echo "</tbody></table>";
    }
} else {
    echo "<p class='bg-warning'>Invalid request</p>";
}

?>

<script>
    window.PrintEvaluationResult = (fucId) => {
        myWindow = window.open(
            '/faculty-course-assignment/generate-evaluation-result-v2?facuid=' + fucId + '&ta=0&__loyout_print=1',
            'title=Generate Evaluation Report V2',
            'height=900,width=800');
    }

    window.PrintEvaluationResult3 = (fucId) => {
        myWindow = window.open(
            '/faculty-course-assignment/generate-evaluation-result-v3?facuid=' + fucId + '&ta=0&__loyout_print=1',
            'title=Generate Evaluation Report V3',
            'height=900,width=800');
    }
</script>