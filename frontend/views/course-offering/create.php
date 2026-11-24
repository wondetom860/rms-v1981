<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseOfferingTemp */

$this->title = Yii::t('rms', 'Create Course Offering Temp');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Course Offering Temps'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-offering-temp-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
