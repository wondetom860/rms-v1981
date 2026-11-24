<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\ProjectGroupName */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'Project Group Name',
]) . $model->pg_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Project Group Names'), 'url' => ['index']];
// $this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="project-group-name-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
