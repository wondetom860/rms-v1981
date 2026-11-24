<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\ReadmissionCalculationControl */

$this->title = Yii::t('decirs', 'Create Readmission Calculation Control');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Readmission Calculation Controls'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="readmission-calculation-control-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
