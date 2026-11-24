<?php

/**
 * 
 * @var yii\web\View $this
 */
//@var $modelClass \frontend\modules\v1\resource\Person
?>
<div class="v1-default-index">
    <h1><?= "RMS-Api" ?></h1>
    <p>
        Use the following URL to access the API endpoints:<br>
        Departments: <code><?= \yii\helpers\Url::to(['/api1/department'], true) ?></code><br>
        Programs: <code><?= \yii\helpers\Url::to(['/api1/program'], true) ?></code><br>
        Courses: <code><?= \yii\helpers\Url::to(['/api1/course'], true) ?></code><br>
        Offerings: <code><?= \yii\helpers\Url::to(['/api1/offering'], true) ?></code><br>
        Students: <code><?= \yii\helpers\Url::to(['/api1/student'], true) ?></code><br>
        Instructors: <code><?= \yii\helpers\Url::to(['/api1/instructor'], true) ?></code><br>
    </p>
</div>