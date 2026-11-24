<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ClassSchedule\models\CsClassTimeDistribution */

$this->title = Yii::t('rms', 'Update Cs Class Time Distribution: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Cs Class Time Distributions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="cs-class-time-distribution-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
