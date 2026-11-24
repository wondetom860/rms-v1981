<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ClassSchedule\models\CsAvailableClassRoom */

$this->title = Yii::t('rms', 'Add Available Class Room');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Available Class Rooms'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cs-available-class-room-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
