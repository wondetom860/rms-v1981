<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\Department */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Department',
]) . $model->dept_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Departments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->dept_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="department-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
