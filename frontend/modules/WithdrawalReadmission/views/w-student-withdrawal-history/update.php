<?php

use yii\helpers\Html;

/** @var yii\web\View $this */
/* @var $model frontend\modules\WithdrawalReadmission\models\WStudentWithdrawalHistory */

$this->title = Yii::t('rms', 'Update W Student Withdrawal History: {name}', [
    'name' => $model->id,
]);
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Withdrawal/Readmission'), 'url' => ['/WithdrawalReadmission/']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Student Withdrawal Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = ['label' => $model->id, 'url' => ['view', 'id' => $model->id]];
$this->params['breadcrumbs'][] = Yii::t('rms', 'Update');
?>
<div class="wstudent-withdrawal-history-update">

    <h3><?= Html::encode($this->title) ?></h3>

    <?= $this->render(Yii::$app->user->can('Student') ? '_form_std' : '_form_hod', [
        'model' => $model,
        'modelSemreg' => $modelSemreg,
    ]) ?>

</div>
