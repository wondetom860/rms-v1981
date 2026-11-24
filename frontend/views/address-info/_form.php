<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\AddressInfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="address-info-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'address_country_relId')->dropdownList(ArrayHelper::map(\backend\models\Nationality::find()->all(),'id','name'),['prompt'=>'Select Country']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'address_region_relId')->dropdownList(ArrayHelper::map(\backend\models\Region::find()->all(),'id','region_name'),['prompt'=>'Select Region']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'address_zone')->textarea(['rows' => 2]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'address_woreda')->textarea(['rows' => 2]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'address_town')->textarea(['rows' => 2]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'address_kebele')->textarea(['rows' => 2]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'address_hourse_no')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Save and continue') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
        
    </div>

    

    

    

    

    

    <?php ActiveForm::end(); ?>

</div>
