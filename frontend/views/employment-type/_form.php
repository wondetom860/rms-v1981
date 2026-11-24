<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\EmploymentType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employment-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'et_name')->textInput(['maxlength' => true]) ?>    
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'remarks')->textInput() ?>
        </div><div class="col-md-3">
            <?= $form->field($model, 'cont_renewal_interval_month')->textInput()->hint('set empty for permannent basis employees.') ?>
        </div>
        
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
