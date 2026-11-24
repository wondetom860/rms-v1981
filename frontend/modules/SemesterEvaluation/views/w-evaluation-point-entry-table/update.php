<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WEvaluationPointEntryTable */

$this->title = Yii::t('decris', 'Update {modelClass}: ', [
    'modelClass' => 'Evaluation Point Entry Table',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Evaluation Point Entry Tables'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decris', 'Update');
?>
<div class="wevaluation-point-entry-table-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
