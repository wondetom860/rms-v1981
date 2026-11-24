<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\StdStatusCalculationControlDefault */

$this->title = Yii::t('rms', 'New Status Calculation Control(Default)');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Status Calculation Intervals (Default)'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="std-status-calculation-control-default-create">

    <h3><?= Html::encode($this->title) ?></h3>
    <!-- <hr> -->

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
