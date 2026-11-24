<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\MilitaryRankCategory */

$this->title = Yii::t('rms', 'Update Military Rank Category: {name}', [
    'name' => $model->category_name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Military Rank Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->category_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="military-rank-category-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
