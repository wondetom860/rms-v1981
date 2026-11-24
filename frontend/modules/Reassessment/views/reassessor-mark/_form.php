<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Reassessment\models\ReassessorMark */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reassessor-mark-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ras_relId')->textInput() ?>

    <?= $form->field($model, 'ras_ima_relId')->textInput() ?>

    <?= $form->field($model, 'ras_original_mark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ras_changed_to')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
