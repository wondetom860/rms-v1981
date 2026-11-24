<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Depal\models\DepalCategorizedCandidates */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="depal-categorized-candidates-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'setting_relId')->textInput() ?>

    <?= $form->field($model, 'category_relId')->textInput() ?>

    <?= $form->field($model, 'criteria_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'candidates')->textInput() ?>

    <?= $form->field($model, 'finalized')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
