<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\ClearanceAllowedStudent */

$this->title = Yii::t('rms', 'Update Clearance Allowed Student: {name}', [
    'name' => $model->cas_full_name,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Clearance Allowed Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->cas_full_name, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="clearance-allowed-student-update">

    <h3 style="border-bottom: 1px solid black;"><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form3', [
        'model' => $model,
    ]) ?>

</div>
