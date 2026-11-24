<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model frontend\models\HonorListControl */

$this->title = Yii::t('rms', 'Create Honor List Control');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Honor List Controls'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="honor-list-control-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
