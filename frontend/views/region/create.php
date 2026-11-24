<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\Region */

$this->title = Yii::t('decirs', 'Create Region');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Regions'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="region-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
