<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\CoursePrerequisit */

$this->title = Yii::t('decirs', 'Create Course Prerequisit');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Course Prerequisits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-prerequisit-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
