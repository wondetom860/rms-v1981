<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\models\StudentWithdrawalHistory */

//$this->title = 'Approve Student Withdrawal: {nameAttribute}';
$this->params['breadcrumbs'][] = ['label' => 'Student Withdrawal Histories', 'url' => ['approve-index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['approve-view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = 'Update';
?>
<div class="student-withdrawal-history-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_formapprove', [
        'model' => $model,
        //'studentId'=>$studentId,
        'CourseCode'=>$CourseCode,
        'CourseTitle'=>$CourseTitle,
        'CourseCrHr'=>$CourseCrHr,
        'Grade'=>$Grade,
        'Points'=>$Points,
        'firstName'=>$firstName,
        'middleName'=>$middleName,
        'lastName'=>$lastName,
    ]) ?>

</div>
