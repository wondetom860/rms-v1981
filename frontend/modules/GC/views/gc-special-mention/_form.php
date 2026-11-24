<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\GC\models\GcSpecialMention */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gc-special-mention-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'gsm_minCGPA')->textInput() ?>

    <?= $form->field($model, 'gsm_maxCGPA')->textInput() ?>

    <?= $form->field($model, 'gsm_name')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
