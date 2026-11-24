<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\ProjectCourseSetup */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'Project Course Setup',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Project Course Setups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="project-course-setup-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
