<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;


/* @var yii\web\View $this 
/* @var backend\models\EmergencyContact $model
/* @var $form yii\widgets\ActiveForm 
*/
?>

<div class="emergency-contact-form">

    <?php $form = ActiveForm::begin(); ?>

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
            <div class="col-md-3 col-sm-6">
                <?= $form->field($model, 'ec_phone_mobile')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3 col-sm-6">
                <?= $form->field($model, 'ec_email')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3 col-sm-6">
                <?= $form->field($model, 'ec_address_relId')->textInput(['readOnly'=>true]) ?>
            </div>
            <div class="col-md-3 col-sm-6"><br>
                <div class="form-group" style="margin-top: 5px;">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>
    

    

    

    <?php ActiveForm::end(); ?>

</div>
