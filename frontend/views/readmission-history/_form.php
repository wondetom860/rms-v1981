<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\ReadmissionHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="readmission-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php //echo $form->field($model, 'rh_student_relId')->textInput() ?>

    <?php //echo $form->field($model, 'rh_academicCal_relId')->textInput() ?>

    <?php //echo $form->field($model, 'rh_semester_reiId')->textInput() ?>
    <div class="row">
        <div class="col-md-3">
         <?= $form->field($model, 'rh_request')->dropDownList(['options'=>'Chose Request','1'=>'Re-admission']) ?>  
         </div>  
    </div>
    

    <?php //echo $form->field($model, 'rh_status')->textInput() ?>

    <?php //echo $form->field($model, 'rh_updatedBy')->textInput() ?>

    <?php //echo $form->field($model, 'rh_updatedAt')->textInput() ?>

    <?php //echo $form->field($model, 'rh_createdAt')->textInput() ?>

    <?php //echo $form->field($model, 'rh_createdBy')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
