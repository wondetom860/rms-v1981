<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\StudentGroup */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'Student Group',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Student Groups'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="student-group-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
