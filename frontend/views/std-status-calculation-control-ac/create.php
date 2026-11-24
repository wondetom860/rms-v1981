<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\StdStatusCalculationControlAc */
$this->title = "Status Calculation Control (AC)";
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Status Calculation Controls'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="std-status-calculation-control-ac-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
