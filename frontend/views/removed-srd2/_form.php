<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\RemovedSrd2 */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="removed-srd2-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'semreg_relId')->textInput() ?>

    <?= $form->field($model, 'std_relId')->textInput() ?>

    <?= $form->field($model, 'coff2det_relId')->textInput() ?>

    <?= $form->field($model, 'srd2_serialized')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'cont_ass_marks')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'total_mark')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'grade_relId')->textInput() ?>

    <?= $form->field($model, 'hod_approved')->textInput() ?>

    <?= $form->field($model, 'hod_approved_at')->textInput() ?>

    <?= $form->field($model, 'hod_approved_by')->textInput() ?>

    <?= $form->field($model, 'reg_approved')->textInput() ?>

    <?= $form->field($model, 'reg_approved_at')->textInput() ?>

    <?= $form->field($model, 'reg_approved_by')->textInput() ?>

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
