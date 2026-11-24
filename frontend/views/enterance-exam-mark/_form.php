<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\EnteranceExamMark */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="enterance-exam-mark-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="hidden">
    <?= $form->field($model, 'ar_request')->textInput(['value' => 1]) ?>
    <?= $form->field($model, 'std_id')->textInput() ?>
    </div>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'eem_subject')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'eem_reference_number')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'eem_year')->textInput(['maxlength' => true,'placeholder'=>'2009']) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'eem_markObtained')->textInput() ?>
            </div>
            <div class="col-md-2"><br>
                <div class="form-group" style="margin-top: 5px;">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
