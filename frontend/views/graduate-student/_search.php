<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\GraduateStudentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="graduate-student-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'gstd_user_relId') ?>

    <?= $form->field($model, 'gstd_adm_relId') ?>

    <?= $form->field($model, 'gstd_prog_relId') ?>

    <?= $form->field($model, 'gstd_dept_relId') ?>

    <?php // echo $form->field($model, 'gstd_spec_relId') ?>

    <?php // echo $form->field($model, 'gstd_sponsorship_relId') ?>

    <?php // echo $form->field($model, 'gstd_adm_year') ?>

    <?php // echo $form->field($model, 'gstd_grad_year') ?>

    <?php // echo $form->field($model, 'gstd_CGPA') ?>

    <?php // echo $form->field($model, 'gstd_date_of_graduation') ?>

    <?php // echo $form->field($model, 'gstd_doc_collected') ?>

    <?php // echo $form->field($model, 'gstd_doc_collected_at') ?>

    <?php // echo $form->field($model, 'gsdt_prepared_by') ?>

    <?php // echo $form->field($model, 'gstd_doc_signed_by') ?>

    <?php // echo $form->field($model, 'gstd_doc_signed_at') ?>

    <?php // echo $form->field($model, 'gstd_doc_signed') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
