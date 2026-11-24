<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Depal\models\DepalDeptQuota */

$this->title = Yii::t('rms', 'Create Depal Dept Quota');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Depal Dept Quotas'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="depal-dept-quota-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
