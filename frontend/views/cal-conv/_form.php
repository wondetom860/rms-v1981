<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\CalConv */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cal-conv-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cc_ger_cal')->textInput() ?>

    <?= $form->field($model, 'cc_et_cal')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
