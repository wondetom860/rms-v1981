<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseDropHistory */

$this->title = Yii::t('rms', 'Create Course Drop History');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Course Drop Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-drop-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
