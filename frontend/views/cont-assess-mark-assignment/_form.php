<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ContAssessMarkAssignment */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cont-assess-mark-assignment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ma_catagory_relId')->textInput() ?>

    <?= $form->field($model, 'ma_instance')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ma_possiblePoint')->textInput() ?>

    <?= $form->field($model, 'ma_created_at')->textInput() ?>

    <?= $form->field($model, 'ma_created_by')->textInput() ?>

    <?= $form->field($model, 'ma_updated_by')->textInput() ?>

    <?= $form->field($model, 'ma_updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
