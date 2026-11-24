<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\AddHistory */

$this->title = Yii::t('rms', 'Request Course Add');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Add Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="add-history-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
