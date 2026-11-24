<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\models\ContAssessCatagoryName */

$this->title = Yii::t('rms', 'Update {modelClass}: ', [
    'modelClass' => 'Cont Assess Catagory Name',
]) . $model->id;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Cont Assess Catagory Names'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="cont-assess-catagory-name-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
