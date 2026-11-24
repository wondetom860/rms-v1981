<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\AddHistory */

$this->title = Yii::t('rms', 'Update Add History: {name}', [
    'name' => $model->ahStdRel->getFullName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Add Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="add-history-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
