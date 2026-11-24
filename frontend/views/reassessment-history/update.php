<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model backend\models\ReassessmentHistory */

$this->title = Yii::t('rms', 'Update Reassessment History: {name}', [
    'name' => $model->rasStdRel->getFullName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Reassessment Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="reassessment-history-update">

    <h4><?= Html::encode($this->title) ?></h4>

    <?= Yii::$app->user->can('DepartmentHead') ? $this->render('_form-Hod', [
        'model' => $model,
    ]) : $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
