<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\AttendancedetailSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attendancedetail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ad_attendance_relId') ?>

    <?= $form->field($model, 'ad_student_relId') ?>

    <?= $form->field($model, 'ad_status') ?>

    <?= $form->field($model, 'ad_remark') ?>

    <div class="form-group">
        <?= Html::submitButton('Search', ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton('Reset', ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
