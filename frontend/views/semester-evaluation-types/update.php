<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluationTypes */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Semester Evaluation Types',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Semester Evaluation Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->semev_type_desc, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="semester-evaluation-types-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
