<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\StudentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="student-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'std_user_relId') ?>

    <?= $form->field($model, 'std_regId') ?>

    <?= $form->field($model, 'std_type') ?>

    <?= $form->field($model, 'std_dept_relId') ?>

    <?php // echo $form->field($model, 'std_spec_relId') ?>

    <?php // echo $form->field($model, 'std_clslvl_relId') ?>

    <?php // echo $form->field($model, 'std_prog_relId') ?>

    <?php // echo $form->field($model, 'std_admission_relId') ?>

    <?php // echo $form->field($model, 'std_acadYear_relId') ?>

    <?php // echo $form->field($model, 'std_group_realId') ?>

    <?php // echo $form->field($model, 'std_semester-status') ?>

    <?php // echo $form->field($model, 'std_created_at') ?>

    <?php // echo $form->field($model, 'std_created_by') ?>

    <?php // echo $form->field($model, 'std_updated_at') ?>

    <?php // echo $form->field($model, 'std_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
