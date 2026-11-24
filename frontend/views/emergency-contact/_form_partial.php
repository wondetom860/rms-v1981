<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this 
 * @var backend\models\EmergencyContact $model
 * @var $form yii\widgets\ActiveForm 
 **/
?>

<div class="emergency-contact-form">

    <?php $form = ActiveForm::begin(['action' => ['emergency-contact/update?id=' . $model->id]]); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <?= $form->field($model, 'ec_first_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3 col-sm-6">
                <?= $form->field($model, 'ec_last_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3 col-sm-6">
                <?= $form->field($model, 'ec_relationShip')->textarea(['rows' => 1]) ?>
            </div>
            <div class="col-md-3 col-sm-6">
                <?= $form->field($model, 'ec_phone_home')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4 col-sm-6">
                <?= $form->field($model, 'ec_phone_mobile')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-5 col-sm-6">
                <?= $form->field($model, 'ec_email')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-2 col-sm-6"><br>
                <div class="form-group" style="margin-top: 5px;">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>