<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseOfferingTemp */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="course-offering-temp-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cot_acadYear_relId')->textInput() ?>

    <?= $form->field($model, 'cot_sem_relId')->textInput() ?>

    <?= $form->field($model, 'cot_adm_relId')->textInput() ?>

    <?= $form->field($model, 'cot_prog_relId')->textInput() ?>

    <?= $form->field($model, 'cot_dept_relId')->textInput() ?>

    <?= $form->field($model, 'cot_clslvel_relId')->textInput() ?>

    <?= $form->field($model, 'cot_spec_relId')->textInput() ?>

    <?= $form->field($model, 'cot_cocat_IDS')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'cot_created_at')->textInput() ?>

    <?= $form->field($model, 'cot_created_by')->textInput() ?>

    <?= $form->field($model, 'cot_updated_at')->textInput() ?>

    <?= $form->field($model, 'cot_updated_by')->textInput() ?>

    <?= $form->field($model, 'cot_acProg_relId')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
