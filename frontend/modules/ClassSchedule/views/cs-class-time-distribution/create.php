<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ClassSchedule\models\CsClassTimeDistribution */

$this->title = Yii::t('rms', 'Create Cs Class Time Distribution');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Cs Class Time Distributions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cs-class-time-distribution-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
