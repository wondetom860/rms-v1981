<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\Region */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="region-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'region_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'region_created_by')->textInput() ?>

    <?= $form->field($model, 'region_created_at')->textInput() ?>

    <?= $form->field($model, 'region_updated_by')->textInput() ?>

    <?= $form->field($model, 'region_updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
