<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ProjectGroupMembersSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-group-members-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'pgm_pgSetup_relId') ?>

    <?= $form->field($model, 'pgm_std_relId') ?>

    <?= $form->field($model, 'pgm_enroll_type') ?>

    <?= $form->field($model, 'pgm_grade_relId') ?>

    <?php // echo $form->field($model, 'pgm_gradeApproved') ?>

    <?php // echo $form->field($model, 'pgm_gradeApproved_by') ?>

    <?php // echo $form->field($model, 'pgm_gradeApproved_at') ?>

    <?php // echo $form->field($model, 'pgm_advisor_comment') ?>

    <?php // echo $form->field($model, 'pgm_created_at') ?>

    <?php // echo $form->field($model, 'pgm_craeted_by') ?>

    <?php // echo $form->field($model, 'pgm_updated_by') ?>

    <?php // echo $form->field($model, 'pgm_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
