<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\GcSpecialMention */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gc-special-mention-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'gsm_maxCGPA')->textInput() ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'gsm_minCGPA')->textInput() ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'gsm_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3"><br>
            <div class="form-group" style="margin-top: 5px;">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
