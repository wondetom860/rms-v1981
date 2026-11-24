<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration3 */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-registration3-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'semreg_stud_relId')->textInput() ?>

    <?= $form->field($model, 'semreg_sem_relId')->textInput() ?>

    <?= $form->field($model, 'semreg_coff_relId')->textInput() ?>

    <?= $form->field($model, 'semreg_coff_relIds')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'semreg_mark_obtained')->textInput() ?>

    <?= $form->field($model, 'semreg_created_on')->textInput() ?>

    <?= $form->field($model, 'semreg_created_by')->textInput() ?>

    <?= $form->field($model, 'semreg_updated_on')->textInput() ?>

    <?= $form->field($model, 'semreg_updated_by')->textInput() ?>

    <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
