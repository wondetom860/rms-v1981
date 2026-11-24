<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\GradeScale */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Grade Scale',
]) . $model->getGradeInterval();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Grade Scales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->gs_ltr_grade, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="grade-scale-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
