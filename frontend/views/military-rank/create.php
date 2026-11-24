<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\MilitaryRank */

$this->title = Yii::t('rms', 'Create Military Rank');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Military Ranks'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="military-rank-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
