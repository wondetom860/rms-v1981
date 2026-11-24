<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\InstanceMarkAssignment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="instance-mark-assignment-form row">

    <?php $form = ActiveForm::begin(); ?>

    <div class="col-md-3" style="display: none;">
        <?= $form->field($model, 'ima_catagorySetup_relId')->textInput() ?>
    </div>
    <div class="col-md-3">
        <h4>Category: <?= $model->imaCatagorySetupRel->catCatagoryRel->catagory_name ?></h4>
    </div>
    <div class="col-md-3">
        <?= $form->field($model, 'ima_instanceName')->textInput(['maxlength' => true]) ?>
    </div>
    
    <div class="col-md-3">
        <?= $form->field($model, 'ima_possiblePoint')->textInput() ?>
    </div>
    <div class="col-md-3">
        <div class="form-group"><br>
            <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
