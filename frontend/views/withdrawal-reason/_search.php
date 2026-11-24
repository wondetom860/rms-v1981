<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\WithdrawalReasonSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="withdrawal-reason-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'withdrawalres_description') ?>

    <?= $form->field($model, 'withdrawalres_updatedAt') ?>

    <?= $form->field($model, 'withdrawalres_createdBy_relId') ?>

    <?= $form->field($model, 'withdrawalres_createdAt') ?>

    <?php // echo $form->field($model, 'withdrawalres_updatedby_relId') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
