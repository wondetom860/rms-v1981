<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\MilitaryRankCategory */

$this->title = Yii::t('rms', 'Create Military Rank Category');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Military Rank Categories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="military-rank-category-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
