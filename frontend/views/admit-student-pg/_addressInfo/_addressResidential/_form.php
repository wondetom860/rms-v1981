<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/* @var $model backend\models\AddressInfo */
/* @var $form yii\widgets\ActiveForm */

$model = &$sessionForm['models']['modelAddressRedencial'];
$model->_indicator = 2;
?>

<div class="address-info-form">

    <?php $form = ActiveForm::begin(['id'=>'modelAddressRedencial']); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'address_country_relId')->dropdownList(ArrayHelper::map(\backend\models\Nationality::find()->all(),'id','name'),
                        [
                            'prompt'=>'Select Address - Country'
                        ])
                ->label('Country') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'address_region_relId')->dropdownList(ArrayHelper::map(\backend\models\Region::find()->all(),'id','region_name'),
                            [
                                'prompt'=>'Select Address - Region'
                            ])
                    ->label('Region') ?>
                <?php
                    echo Html::activeHiddenInput($model, "_indicator",['class'=>'hidden_input_indicator_2']);
                ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'address_zone')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'address_woreda')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'address_town')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'address_kebele')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'address_hourse_no')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-3"><br>
                <div class="form-group" style="margin-top: 5px;">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Save and continue') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    $("#modelAddressRedencial input,select").change(function(e){
        $('#modelAddressRedencial .btn').prop('disabled',false);
    });
    $("#modelAddressRedencial").on('submit',function(e){
        e.preventDefault();
        $ak = submitMaster($("#modelAddressRedencial")[0].id);
        if ($ak == 1) {$("#modelAddressRedencial .btn").prop('disabled',true);}
        return false;
    });
</script>