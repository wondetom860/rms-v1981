<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\StudentSemesterStatusName */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Student Semester Status Name',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Student Semester Status Names'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="student-semester-status-name-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
