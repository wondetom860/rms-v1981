<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluationScale */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'Semester Evaluation Scale',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Semester Evaluation Scales'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="semester-evaluation-scale-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
