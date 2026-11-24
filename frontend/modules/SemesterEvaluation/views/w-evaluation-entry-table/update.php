<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WEvaluationEntryTable */

$this->title = Yii::t('decris', 'Update {modelClass}: ', [
    'modelClass' => 'Wevaluation Entry Table',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Wevaluation Entry Tables'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decris', 'Update');
?>
<div class="wevaluation-entry-table-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
