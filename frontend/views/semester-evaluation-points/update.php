<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluationPoints */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Semester Evaluation Points',
]) . "[{$model->id}]";
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Semester Evaluation Points'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="semester-evaluation-points-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
