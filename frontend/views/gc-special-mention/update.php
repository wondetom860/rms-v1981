<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\models\GcSpecialMention */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Gc Special Mention',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Gc Special Mentions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="gc-special-mention-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
