<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\WithdrawalReason */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="withdrawal-reason-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'withdrawalres_type')->textInput(['maxlength' => true,'placeholder'=>'NORMAL, ILLEGAL. CHEATING'])->label('Withdrawal Type') ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'withdrawalres_description')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'remove_course_enrolls')->checkBox() ?>
            <?= $form->field($model, 'readmittable')->checkBox() ?>
        </div>
        <div class="col-md-2"><br>
            <div class="form-group" style="margin-top: 7px">
                <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    

    <?php ActiveForm::end(); ?>

</div>
