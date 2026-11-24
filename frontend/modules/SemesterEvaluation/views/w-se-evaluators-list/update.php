<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WSeEvaluatorsList */

$this->title = Yii::t('decris', 'Update {modelClass}: ', [
    'modelClass' => 'Wse Evaluators List',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Wse Evaluators Lists'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decris', 'Update');
?>
<div class="wse-evaluators-list-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
