<?php

use yii\helpers\Html;

/* @var $this yii\web\View */
/* @var $model frontend\modules\GC\models\GcEntry */

$this->title = Yii::t('rms', 'Update GC Entry: {name}', [
    'name' => $model->header_title,
]);
$model->senate_approve_date_str = date('d-m-Y',$model->senate_approve_date);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Gc Entries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->header_title, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="gc-entry-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
