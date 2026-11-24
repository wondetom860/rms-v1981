<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\PgPostSecondarySchool */

$this->title = Yii::t('decirs', 'Update {modelClass}: ', [
    'modelClass' => 'Pg Post Secondary School',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Pg Post Secondary Schools'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('decirs', 'Update');
?>
<div class="pg-post-secondary-school-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
