<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\models\HonorListControl */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Honor List Control',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Honor List Controls'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="honor-list-control-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
