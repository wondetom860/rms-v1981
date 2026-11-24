<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ClassSchedule\models\CsAvailableClassRoom */

$this->title = Yii::t('rms', 'Update Available Class Room: {name}', [
    'name' => $model->room,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Available Class Rooms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->room, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="cs-available-class-room-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
