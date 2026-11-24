<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ClassSchedule\models\CsClassTimeDistribution */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cs-class-time-distribution-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'work_day_relId')->textInput() ?>

    <?= $form->field($model, 'shift')->dropDownList([ 'Morning' => 'Morning', 'Afternoon' => 'Afternoon', 'Another' => 'Another', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'class_count')->textInput() ?>

    <?= $form->field($model, 'start_time')->textInput() ?>

    <?= $form->field($model, 'end_time')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
