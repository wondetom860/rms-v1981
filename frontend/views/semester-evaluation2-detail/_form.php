<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluation2Detail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-evaluation2-detail-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'semev2det_semev_relId')->textInput() ?>

    <?= $form->field($model, 'semev2det_evaluator_relId')->textInput() ?>

    <?= $form->field($model, 'semev2det_completed')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
