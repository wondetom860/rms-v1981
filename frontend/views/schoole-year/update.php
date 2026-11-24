<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\SchoolYear */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'School Year',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'School Years'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="school-year-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
