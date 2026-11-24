<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\EducationInformation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="education-information-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ei_G10_doc_scan')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'ei_G12_doc_scan')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'ei_enteranceMarkPoint')->textInput() ?>

    <?= $form->field($model, 'ei_created_at')->textInput() ?>

    <?= $form->field($model, 'ei_updated_at')->textInput() ?>

    <?= $form->field($model, 'ei_created_by')->textInput() ?>

    <?= $form->field($model, 'ei_updated_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Save and continue') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
