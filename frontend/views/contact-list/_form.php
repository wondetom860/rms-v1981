<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ContactList */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contact-list-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2">
                <?= $form->field($model, 'cl_catagory_relId')->dropdownList(yii\helpers\ArrayHelper::map(\backend\models\ContactCatagory::find()->all(),'id','cat_name'),['prompt'=>'Select Category']) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'cl_full_name')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'cl_phone_office')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'cl_phone_mobile')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'cl_phone_ext')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-2">
                <div class="form-group" style="margin-top: 5px"><br>
                    <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>
    </div>

    

    <?php ActiveForm::end(); ?>

</div>
