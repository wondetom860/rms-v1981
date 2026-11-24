<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ProjectGroupMembers */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-group-members-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pgm_pgSetup_relId')->textInput() ?>

    <?= $form->field($model, 'pgm_std_relId')->textInput() ?>

    <?= $form->field($model, 'pgm_enroll_type')->textInput() ?>

    <?= $form->field($model, 'pgm_grade_relId')->textInput() ?>

    <?= $form->field($model, 'pgm_gradeApproved')->textInput() ?>

    <?= $form->field($model, 'pgm_gradeApproved_by')->textInput() ?>

    <?= $form->field($model, 'pgm_gradeApproved_at')->textInput() ?>

    <?= $form->field($model, 'pgm_advisor_comment')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pgm_created_at')->textInput() ?>

    <?= $form->field($model, 'pgm_craeted_by')->textInput() ?>

    <?= $form->field($model, 'pgm_updated_by')->textInput() ?>

    <?= $form->field($model, 'pgm_updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
