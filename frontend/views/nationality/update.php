<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\Nationality */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'Nationality',
]) . $model->name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Nationalities'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="nationality-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
