<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\Semester */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'Semester',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Semesters'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="semester-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
