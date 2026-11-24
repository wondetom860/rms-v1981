<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model common\models\AuthOrg8n */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="auth-org8n-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row bg-success">
        <div class="col-md-6">
            <div class="col-md-12">
                <?= $form->field($model, 'abrvn')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-12">
                 <?= $form->field($model, 'name')->textarea(['rows' => 1]) ?>
            </div>
        </div>
        <div class="col-md-6">
            <div class="col-md-12">
                <?= $form->field($model, 'logo_file')->fileInput() ?>
            </div>
        </div>
    </div><hr>
    <div class="row bg-info">
        <div class="col-md-3">
            <?= $form->field($model, 'website')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
             <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'phone1')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'phone2')->textInput(['maxlength' => true]) ?>
        </div>
    </div><hr>
    <div class="row bg-warning">
        <div class="col-md-3">
            <?= $form->field($model, 'active_from')->textInput() ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'active_to')->textInput() ?>
        </div>

        <div class="col-md-6">
            <?= $form->field($model, 'lic_entry')->textInput(['maxlength' => true]) ?>
        </div>
    </div><hr>
    <div class="row">
        <div class="form-group pull-right">
            <?= Html::submitButton(Yii::t('decris', 'Save'), ['class' => 'btn btn-success']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
