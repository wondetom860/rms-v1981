<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\Course */

$this->title = Yii::t('rms', 'Update Course:  {modelClass}: ', [
    'modelClass' => $model->course_title,
]) . "[{$model->course_crdthr}-{$model->course_lecture_hour}-{$model->course_lab_hour}/{$model->course_tutorial_hour}]";
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Courses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->course_title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="course-update">

    <h3><i><?= Html::encode($this->title) ?></i></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
