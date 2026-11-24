<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/** @var backend\models\Program $model */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Program',
]) . $model->program_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Programs'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->program_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
$model->total_crdt_hrs = (int)$model->total_crdt_hrs; // Ensure total_crdt_hrs is an integer
?>
<div class="program-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
