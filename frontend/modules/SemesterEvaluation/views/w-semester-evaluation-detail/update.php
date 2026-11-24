<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WSemesterEvaluationDetail */

$this->title = Yii::t('decris', 'Update {modelClass}: ', [
    'modelClass' => 'Wsemester Evaluation Detail',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decris', 'Wsemester Evaluation Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decris', 'Update');
?>
<div class="wsemester-evaluation-detail-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
