<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\ProjectCourseSetup */

$this->title = Yii::t('decirs', 'Create Project Course Setup');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Project Course Setups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="project-course-setup-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
