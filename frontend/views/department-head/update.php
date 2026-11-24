<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\DepartmentHead */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Department Head',
]) . $model->dhDeptRel->dept_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Department Heads'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="department-head-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
