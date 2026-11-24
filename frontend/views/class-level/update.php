<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\ClassLevel */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Class Level',
]) . $model->clslvl_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Class Levels'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->clslvl_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="class-level-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
