<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\TouchedGradesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="touched-grades-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'tg_acadYear_relId') ?>

    <?= $form->field($model, 'tg_sem_relId') ?>

    <?= $form->field($model, 'tg_prog_relId') ?>

    <?= $form->field($model, 'tg_clslvl_relId') ?>

    <?php // echo $form->field($model, 'tg_semreg_relId') ?>

    <?php // echo $form->field($model, 'tg_srd2_relId') ?>

    <?php // echo $form->field($model, 'tg_beforeGrade_relId') ?>

    <?php // echo $form->field($model, 'tg_afterGrade_relId') ?>

    <?php // echo $form->field($model, 'tg_reason') ?>

    <?php // echo $form->field($model, 'tg_created_at') ?>

    <?php // echo $form->field($model, 'tg_created_by') ?>

    <?php // echo $form->field($model, 'tg_updated_at') ?>

    <?php // echo $form->field($model, 'tg_updated_by') ?>

    <?php // echo $form->field($model, 'tg_approved_by') ?>

    <?php // echo $form->field($model, 'tg_approved_at') ?>

    <?php // echo $form->field($model, 'tg_operation_completed') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
