<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluationDetail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-evaluation-detail-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'semevdet_semev_relId')->textInput() ?>

    <?= $form->field($model, 'semevdet_evalType_relId')->textInput() ?>

    <?= $form->field($model, 'semevdet_completed')->textInput() ?>

    <?= $form->field($model, 'semevdet_averagePoint')->textInput() ?>

    <?= $form->field($model, 'semevdet_evaluee_relId')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
