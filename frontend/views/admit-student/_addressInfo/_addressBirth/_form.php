<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/* @var $model backend\models\AddressInfo */
/* @var $form yii\widgets\ActiveForm */
$model = &$sessionForm['models']['modelAddressBirth'];
$model->_indicator = 1;
?>

<div class="address-info-form">

    <?php $form = ActiveForm::begin(['id'=>'modelAddressBirth']); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'address_country_relId')->dropdownList(ArrayHelper::map(\backend\models\Nationality::find()->all(),'id','name'),
                        [
                            'prompt'=>'Select Address - Country'
                        ])
                ->label('Country') ?>
                <?php
                echo Html::activeHiddenInput($model, "_indicator",['class'=>'hidden_input_indicator_1']);
                ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'address_region_relId')->dropdownList(ArrayHelper::map(\backend\models\Region::find()->all(),'id','region_name'),
                            [
                                'prompt'=>'Select Address - Region',
                                'id' => 'address_region_relId_address_birth'
                            ])
                    ->label('Region') ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'address_zone')->textInput(['maxlength' => true, 'id' => 'address_zone_relId_address_birth']) ?>
            </div>
             <div class="col-md-3">
                <?= $form->field($model, 'address_woreda')->textInput(['maxlength' => true, 'id' => 'address_woreda_relId_address_birth']) ?>
            </div>
        </div>
        <div class="row">
             <div class="col-md-3">
                <?= $form->field($model, 'address_town')->textInput(['maxlength' => true, 'id' => 'address_town_relId_address_birth']) ?>
            </div>
             <div class="col-md-3">
                <?= $form->field($model, 'address_kebele')->textInput(['maxlength' => true, 'id' => 'address_kebele_relId_address_birth']) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'address_hourse_no')->textInput(['maxlength' => true, 'id' => 'address_house_no_relId_address_birth']) ?>
            </div>
            <div class="col-md-3"><br>
                <div class="form-group" style="margin-top: 5px;">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Save and continue') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
    $("#modelAddressBirth input,select").change(function(e){
        $('#modelAddressBirth .btn').prop('disabled',false);
    });
    $("#modelAddressBirth").on('submit',function(e){
        e.preventDefault();
        $ak = submitMaster($("#modelAddressBirth")[0].id);
        if ($ak == 1) {
            $('#modelAddressBirth .btn').prop('disabled',true);
        }
        return false;
    });
</script>