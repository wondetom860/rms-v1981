<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\EthnicGroup */

$this->title = Yii::t('rms', 'Update Ethnic Group: {name}', [
    'name' => $model->name."({$model->regionRel->region_name})",
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Ethnic Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="ethnic-group-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
