<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\StudentWithdrawalHistorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="student-withdrawal-history-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'swh_student_relId') ?>

    <?= $form->field($model, 'swh_academicyear_relId') ?>

    <?= $form->field($model, 'swh_semester_relId') ?>

    <?= $form->field($model, 'swh_withdrawalreason_relId') ?>

    <?php // echo $form->field($model, 'swh_request_status') ?>

    <?php // echo $form->field($model, 'swh_comment') ?>

    <?php // echo $form->field($model, 'swh_createdBy') ?>

    <?php // echo $form->field($model, 'swh_updatedBy') ?>

    <?php // echo $form->field($model, 'swh_createdAt') ?>

    <?php // echo $form->field($model, 'swh_updatedAt') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
