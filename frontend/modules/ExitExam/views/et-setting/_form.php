<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\ExitExam\models\EtSetting */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="et-setting-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'setting_header_info')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'max_trails')->textInput(['type' => 'NUMBER']) ?>

    <?= $form->field($model, 'pass_mark')->textInput(['maxlength' => true, 'type' => "DECIMAL"]) ?>

    <?= $form->field($model, 'active_status')->dropDownList([1 => 'Active', 0 => 'In-Active']) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>