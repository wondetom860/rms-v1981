<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseDropHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="course-drop-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cdh_srd2_relId')->textInput() ?>

    <?= $form->field($model, 'cdh_std_relId')->textInput() ?>

    <?= $form->field($model, 'cdh_course_relId')->textInput() ?>

    <?= $form->field($model, 'cdh_acadYear_relId')->textInput() ?>

    <?= $form->field($model, 'cdh_sem_relId')->textInput() ?>

    <?= $form->field($model, 'cdh_group_relId')->textInput() ?>

    <?= $form->field($model, 'cdh_clslvl_relId')->textInput() ?>

    <?= $form->field($model, 'cdh_approved_by')->textInput() ?>

    <?= $form->field($model, 'cdh_status')->textInput() ?>

    <?= $form->field($model, 'cdh_created_at')->textInput() ?>

    <?= $form->field($model, 'cdh_updated_at')->textInput() ?>

    <?= $form->field($model, 'cdh_updated_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
