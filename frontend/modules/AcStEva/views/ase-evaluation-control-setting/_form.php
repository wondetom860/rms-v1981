<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseEvaluationControlSetting */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ase-evaluation-control-setting-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'dept_plan_relId')->textInput() ?>

    <?= $form->field($model, 'candidate_staff_relId')->textInput() ?>

    <?= $form->field($model, 'evaluators')->textInput() ?>

    <?= $form->field($model, 'total_percentage')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'finalized')->textInput() ?>

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
