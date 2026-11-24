<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Depal\models\DepalDeptQuota */

$this->title = Yii::t('rms', 'Update Depal Dept Quota: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Depal Dept Quotas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="depal-dept-quota-update">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
