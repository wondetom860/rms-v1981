<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\StdStatusCalculationControlAc */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Status Calculation Control(Ac)',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Status Calculation Controls'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="std-status-calculation-control-ac-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
