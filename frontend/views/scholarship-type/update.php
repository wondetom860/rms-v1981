<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\ScholarshipType */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Sponsorship Type',
]) . $model->schType_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Sponsorship Types'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->schType_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="scholarship-type-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
