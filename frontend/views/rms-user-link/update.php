<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\RmsUserLink */

$this->title = Yii::t('rms', 'Update Rms User Link: {name}', [
    'name' => $model->rmsUserRel->getFullName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Linked Acccounts'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="rms-user-link-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
