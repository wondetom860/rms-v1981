<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseCatalog */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Course Catalog',
]) . $model->cocatCourseRel->course_code . '['.$model->cocatCourseRel->course_title .']';
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Course Catalogs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cocatCourseRel->course_code, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="course-catalog-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
