<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SchoolYear */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="school-year-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sch_year_desc')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sch_year_status')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sch_created_by')->textInput() ?>

    <?= $form->field($model, 'sch_created_at')->textInput() ?>

    <?= $form->field($model, 'sch_updated_by')->textInput() ?>

    <?= $form->field($model, 'sch_updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
