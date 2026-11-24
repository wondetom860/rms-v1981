<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model backend\models\WStudentWithdrawalHistory */

$this->title = Yii::t('decirs', 'Create Wstudent Withdrawal History');
$this->params['breadcrumbs'][] = ['label' => Yii::t('decirs', 'Wstudent Withdrawal Histories'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wstudent-withdrawal-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
