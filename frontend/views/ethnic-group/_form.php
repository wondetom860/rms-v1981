<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\EthnicGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ethnic-group-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'region_relId')->dropdownList(ArrayHelper::map(\backend\models\Region::find()->all(),'id','region_name')) ?>

    <?= $form->field($model, 'name')->textInput(['maxlength' => true]) ?>


    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
