<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\WithdrawalReadmission\models\WStudentReadmissionHistory */

$this->title = Yii::t('rms', 'Create Student Readmission History');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Withdrawal/Readmission'), 'url' => ['/WithdrawalReadmission/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Student Readmission Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wstudent-readmission-history-create">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
