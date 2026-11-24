<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\StudentSemesterStatusHistory */

$this->title = Yii::t('decirs', 'Create Student Semester Status History');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Student Semester Status Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-semester-status-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
