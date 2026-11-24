<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ClassSchedule\models\CsClassScheduleDetail */

$this->title = Yii::t('rms', 'Create Cs Class Schedule Detail');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Cs Class Schedule Details'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cs-class-schedule-detail-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
