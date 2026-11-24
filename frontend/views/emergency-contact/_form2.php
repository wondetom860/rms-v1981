<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/**
 * @var yii\web\View $this 
 * @var backend\models\EmergencyContact $model
 * @var backend\models\AddressInfo $modelAddress
 * @var $form yii\widgets\ActiveForm 
 **/
?>

<div class="emergency-contact-form">

    <?php $form = ActiveForm::begin(['action' => ['emergency-contact/create']]); ?>
    <!-- hidden input for student id -->
    <?= Html::hiddenInput('student_id', $modelStudent->id) ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3 col-sm-6">
                <?= $form->field($model, 'ec_first_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3 col-sm-6">
                <?= $form->field($model, 'ec_last_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3 col-sm-6">
                <?= $form->field($model, 'ec_relationShip')->textInput(['maxlength' => true]) ?>
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
            <div class="col-md-3">
                <?= $form->field($modelAddress, 'address_country_relId')->dropdownList(ArrayHelper::map(\backend\models\Nationality::find()->all(), 'id', 'name'), ['prompt' => 'Select Country']) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($modelAddress, 'address_region_relId')->dropdownList(ArrayHelper::map(\backend\models\Region::find()->all(), 'id', 'region_name'), ['prompt' => 'Select Region']) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($modelAddress, 'address_zone')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($modelAddress, 'address_woreda')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <?= $form->field($modelAddress, 'address_town')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($modelAddress, 'address_kebele')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-4">
                <?= $form->field($modelAddress, 'address_hourse_no')->textInput(['maxlength' => true]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-2 col-sm-6"><br>
                <div class="form-group" style="margin-top: 5px;">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>