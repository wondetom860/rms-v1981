<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluation2Detail */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'Semester Evaluation2 Detail',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Semester Evaluation2 Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="semester-evaluation2-detail-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
