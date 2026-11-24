<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\Specialization */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Specialization',
]) . $model->spec_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Specializations'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->spec_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="specialization-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
