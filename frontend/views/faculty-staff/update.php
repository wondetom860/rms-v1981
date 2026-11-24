<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\FacultyStaff */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Faculty Staff',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Faculty Staff'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="faculty-staff-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
