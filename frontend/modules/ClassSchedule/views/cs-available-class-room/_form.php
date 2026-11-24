<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ClassSchedule\models\CsAvailableClassRoom */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cs-available-class-room-form container" style="background-color: #FDF;">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'room')->textInput(['maxlength' => true]) ?>
        </div>    
        <div class="col-md-4">
            <?= $form->field($model, 'description')->textarea(['rows' => 1]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'num_of_seats')->textarea(['rows' => 1]) ?>
        </div>  
        <div class="col-md-3">
            <?= $form->field($model, 'status')->dropdownList(['1' => 'Available', 0 => 'Not Available'],['prompt' => 'Select Status']) ?>
        </div>    
    </div>
        <h4>Facilities available in the class room: </h4>

    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'white_board')->checkbox() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'black_board')->checkbox() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'projector')->checkbox() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'projector_board')->checkbox() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'room_pc')->checkbox() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'pulpit')->checkbox() ?>
        </div>
    </div>
    <div class="row">
        
        <div class="col-md-12 bg-info"><br>
            <div class="form-group pull-right bg-info">
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
