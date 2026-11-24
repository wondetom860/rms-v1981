<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\WithdrawalReadmission\models\WStudentWithdrawalHistory */

$this->title = Yii::t('rms', 'Student Withdrawal Form');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Withdrawal/Readmission'), 'url' => ['/WithdrawalReadmission/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Student Withdrawal Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wstudent-withdrawal-history-create">

    <h3><?= Html::encode($this->title) ?> >>> <?= $model->swhStdRel->getFullName()."{$model->swhStdRel->stdGroupReal->getGsDisp()}" ?></h3>

    <?= $this->render(Yii::$app->user->can('Student') ? '_form_std' : '_form_hod', [
        'model' => $model,
        'modelSemreg' => $modelSemreg,
    ]) ?>

</div>
