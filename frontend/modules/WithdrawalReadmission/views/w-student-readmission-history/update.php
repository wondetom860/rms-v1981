<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\WithdrawalReadmission\models\WStudentReadmissionHistory */

$this->title = Yii::t('rms', 'Update Student Readmission History: {name}', [
    'name' => $model->srhStdRel->getFullName(),
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Withdrawal/Readmission'), 'url' => ['/WithdrawalReadmission/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Student Readmission Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->srhStdRel->getFullName(), 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="wstudent-readmission-history-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
