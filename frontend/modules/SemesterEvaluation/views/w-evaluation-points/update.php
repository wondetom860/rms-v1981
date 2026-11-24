<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WEvaluationPoints */

$this->title = Yii::t('decris', 'Update {modelClass}: ', [
    'modelClass' => 'Wevaluation Points',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Wevaluation Points'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decris', 'Update');
?>
<div class="wevaluation-points-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
