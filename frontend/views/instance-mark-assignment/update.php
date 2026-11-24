<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\InstanceMarkAssignment */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Instance Mark Assignment',
]) . $model->imaCatagorySetupRel->catCatagoryRel->catagory_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Instance Mark Assignments'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="instance-mark-assignment-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
