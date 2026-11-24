<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\StudentProfileChange */
/* @var $form yii\widgets\ActiveForm */

// var_dump($attributeValues);exit;


?>

<div class="student-profile-change-form">

    <?php $form = ActiveForm::begin(['action' => '/student-profile-change/create']); ?>
    <div class="hidden">
        <?= $form->field($model, 'std_relId')->textInput() ?>
        <?= $form->field($model, 'std_attribute')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'relation_name')->textInput(['maxlength' => true]) ?>
        <?= $form->field($model, 'related_attribute_name')->textInput(['maxlength' => true]) ?>
    </div>
    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'attribute_label')->textInput(['maxlength' => true]) ?>

        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'old_value')->textInput(['maxlength' => true]) ?>

        </div>
    </div>

    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'reason')->textInput(['maxlength' => true]) ?>
        </div>
    </div>

    <div class="row">
        <div class="col-md-10">
            <?= $attributeValues ?
                $form->field($model, 'new_value')->dropDownList(ArrayHelper::map($attributeValues, 'id', $attributeName), ['prompt' => 'Select new value']) :
                $form->field($model, 'new_value')->textInput(['maxlength' => true])
            ?>

        </div>
        <div class="col-md-2"><br>
            <div class="form-group" style="margin-top: 5px;">
                <?= Html::submitButton(Yii::t('rms', 'Place Request'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>




    <?php ActiveForm::end(); ?>

</div>