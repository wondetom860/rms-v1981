<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ContinuousAssessment2 */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="continuous-assessment2-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ca_std_relid')->textInput() ?>

    <?= $form->field($model, 'ca_semreg_relId')->textInput() ?>

    <?= $form->field($model, 'ca_markAssignment_relId')->textInput() ?>

    <?= $form->field($model, 'ca_catagorySetup_relId')->textInput() ?>

    <?= $form->field($model, 'ca_markObtained')->textInput() ?>

    <?= $form->field($model, 'ca_coff_relId')->textInput() ?>

    <?= $form->field($model, 'ca_created_by')->textInput() ?>

    <?= $form->field($model, 'ca_created_at')->textInput() ?>

    <?= $form->field($model, 'ca_updated_by')->textInput() ?>

    <?= $form->field($model, 'ca_updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
