<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseEmploymentTypeBundle */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ase-employment-type-bundle-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'employment_type')->dropDownList([ 'PERMANENT EMPLOYEE' => 'PERMANENT EMPLOYEE', 'PART-TIMER' => 'PART-TIMER', 'GUEST-LECTURER' => 'GUEST-LECTURER', ], ['prompt' => '']) ?>

    <?= $form->field($model, 'applicable_bundles')->textInput() ?>

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
