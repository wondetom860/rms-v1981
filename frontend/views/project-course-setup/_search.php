<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ProjectCourseSetupSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-course-setup-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'pcs_acadYear_relId') ?>

    <?= $form->field($model, 'pcs_sem_relId') ?>

    <?= $form->field($model, 'pcs_gset_relId') ?>

    <?= $form->field($model, 'pcs_course_relId') ?>

    <?php // echo $form->field($model, 'pcs_pgName_relId') ?>

    <?php // echo $form->field($model, 'pcs_advisor') ?>

    <?php // echo $form->field($model, 'pcs_group_leader') ?>

    <?php // echo $form->field($model, 'pcs_title') ?>

    <?php // echo $form->field($model, 'pcs_approved') ?>

    <?php // echo $form->field($model, 'pcs_approved_at') ?>

    <?php // echo $form->field($model, 'pcs_approved_by') ?>

    <?php // echo $form->field($model, 'pcs_created_at') ?>

    <?php // echo $form->field($model, 'pcs_created_by') ?>

    <?php // echo $form->field($model, 'pcs_updated_at') ?>

    <?php // echo $form->field($model, 'pcs_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
