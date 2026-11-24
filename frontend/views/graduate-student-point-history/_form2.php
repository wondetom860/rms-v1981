<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\GraduateStudentPointHistory */
/* @var $form yii\widgets\ActiveForm */
?>
 
<div class="graduate-student-point-history-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'acadYear')->textInput(['readOnly' => true, 'value' => $model->gphRegistrationEntryRel->gphr_acadYear]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'gph_SGPA')->textInput() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'gph_CGPA')->textInput() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'gph_credit_hour_enrolled')->textInput() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'gph_average_point')->textInput() ?>
        </div>
        <div class="col-md-2">
            <div class="form-group pull-right"><br>
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
