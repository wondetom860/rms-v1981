<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\CocatDetail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cocat-detail-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'id')->textInput() ?>

    <?= $form->field($model, 'cocatDet_cocat_relId')->textInput() ?>

    <?= $form->field($model, 'cocatDet_course_relId')->textInput() ?>

    <?= $form->field($model, 'cocatDet_created_by')->textInput() ?>

    <?= $form->field($model, 'cocatDet_created_at')->textInput() ?>

    <?= $form->field($model, 'cocatDet_updated_by')->textInput() ?>

    <?= $form->field($model, 'cocat|Det_updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
