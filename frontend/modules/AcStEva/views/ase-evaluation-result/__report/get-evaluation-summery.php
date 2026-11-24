<?php


echo "<h5><u>Evaluation Summery : Top scorers</u></h5>";

$topScorers = \frontend\modules\AcStEva\models\AseEvaluationResult::find()
    ->innerJoinWith('controlSettingRel')
    ->orderBy(['average_total' => SORT_DESC])
    ->groupBy(['candidate_staff_relId'])
    ->limit(10)
    ->all();
if($topScorers){
    foreach ($topScorers as $evalresult) {
        $evalue = $evalresult->controlSettingRel->candidateStaffRel;
        $avgScore = round($evalresult->average_total, 2);
        echo "<div style='margin: auto; padding: 5px;'>
            <strong>{$evalue->getFullName()}</strong> (ID: {$evalue->id})<br>
            Average Score: <span class='text-success'>{$avgScore}</span>
        </div>";
    }
}
?>