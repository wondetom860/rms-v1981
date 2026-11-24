<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluationResult */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-evaluation-result-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ser_semevdet_relId')->textInput() ?>

    <?= $form->field($model, 'ser_semev_point_relId')->textInput() ?>

    <?= $form->field($model, 'ser_point_given')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
