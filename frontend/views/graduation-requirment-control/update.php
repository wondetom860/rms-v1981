<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\GraduationRequirmentControl */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'Graduation Requirment Control',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Graduation Requirment Controls'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="graduation-requirment-control-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
