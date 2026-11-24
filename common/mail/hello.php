<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $user common\models\User */
$user = $params->coff2detInstRel->instUserRel;
$verifyLink = Yii::$app->urlManager->createAbsoluteUrl(['site/verify-email', 'token' => $user->getFullName()]);
// $attachment = $this->render(
//     '/generate-semester-evaluation-report/evaluation-report-print',
//     ['model' => $params, 'ta' => $ta]
// );

?>
<div class="verify-email">
    <p>Hello <?= Html::encode($user->username) ?>,</p>

    <h3>Academic Staff Semester Performance Evaluation Report</h3>
    <div style="border: 1px dashed black;">
        Dear, <?= $user->getFullName()  ?>, This is your semester performance evaluation result for academic year of <?= $params->getOfferingAcadYearInfo() ?>.
    </div>
</div>