<?php

use yii\helpers\Html;


/** @var yii\web\View $this */
/* @var $model frontend\models\StudentWithdrawalHistory */

$this->title = 'Create Withdrawal';
$this->params['breadcrumbs'][] = ['label' => 'Student Withdrawal Histories', 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-withdrawal-history-create">

    <h1><?= Html::encode($this->title) ?></h1>

    <?= $this->render('_form', [
        'model' => $model,
    ]) ?>

</div>
