<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\StudentSemesterStatusName */

$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Student Semester Status Names'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-semester-status-name-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
