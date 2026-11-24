<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\WGroupSetupSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wgroup-setup-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'gs_group_relId') ?>

    <?= $form->field($model, 'gs_adm_relId') ?>

    <?= $form->field($model, 'gs_prog_relId') ?>

    <?= $form->field($model, 'gs_acadYear_relId') ?>

    <?php // echo $form->field($model, 'gs_dept_relId') ?>

    <?php // echo $form->field($model, 'gs_clslvl_relId') ?>

    <?php // echo $form->field($model, 'gs_spec_relId') ?>

    <?php // echo $form->field($model, 'gs_sem_relId') ?>

    <?php // echo $form->field($model, 'gs_created_by') ?>

    <?php // echo $form->field($model, 'gs_updated_by') ?>

    <?php // echo $form->field($model, 'gs_created_at') ?>

    <?php // echo $form->field($model, 'gs_updated_at') ?>

    <?php // echo $form->field($model, 'gs_status') ?>

    <?php // echo $form->field($model, 'gs_size') ?>

    <?php // echo $form->field($model, 'remark') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
