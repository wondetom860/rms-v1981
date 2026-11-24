<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\Attendancedetail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="attendancedetail-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ad_attendance_relId')->textInput() ?>

    <?= $form->field($model, 'ad_student_relId')->textInput() ?>

    <?= $form->field($model, 'ad_status')->textInput() ?>

    <?= $form->field($model, 'ad_remark')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
