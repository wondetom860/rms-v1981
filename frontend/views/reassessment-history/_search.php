<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ReassessmentHistorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reassessment-history-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ras_std_relId') ?>

    <?= $form->field($model, 'ras_srd2_relId') ?>

    <?= $form->field($model, 'ras_course_relId') ?>

    <?= $form->field($model, 'ras_acadYear_relId') ?>

    <?php // echo $form->field($model, 'ras_sem_relId') ?>

    <?php // echo $form->field($model, 'ras_group_relId') ?>

    <?php // echo $form->field($model, 'ras_clslvl_relId') ?>

    <?php // echo $form->field($model, 'ras_status') ?>

    <?php // echo $form->field($model, 'ras_registrarApprove_status') ?>

    <?php // echo $form->field($model, 'ras_registrarApprove_by') ?>

    <?php // echo $form->field($model, 'ras_registrarApprove_at') ?>

    <?php // echo $form->field($model, 'ras_approved_by') ?>

    <?php // echo $form->field($model, 'ras_bef_grade_relId') ?>

    <?php // echo $form->field($model, 'ras_app_date') ?>

    <?php // echo $form->field($model, 'ras_created_at') ?>

    <?php // echo $form->field($model, 'ras_updated_at') ?>

    <?php // echo $form->field($model, 'ras_updated_by') ?>

    <?php // echo $form->field($model, 'ras_aft_grade_relId') ?>

    <?php // echo $form->field($model, 'ras_prevMark') ?>

    <?php // echo $form->field($model, 'ras_afterMark') ?>

    <?php // echo $form->field($model, 'ras_contAssCatag_relId') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
