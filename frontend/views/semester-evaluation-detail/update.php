<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluationDetail */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'Semester Evaluation Detail',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Semester Evaluation Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="semester-evaluation-detail-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
