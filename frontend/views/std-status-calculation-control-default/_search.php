<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\StdStatusCalculationControlDefaultSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="std-status-calculation-control-default-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sssccd_adm_relId') ?>

    <?= $form->field($model, 'sssccd_prog_relId') ?>

    <?= $form->field($model, 'sssccd_sssn_relId') ?>

    <?= $form->field($model, 'sssccd_max_CGPA') ?>

    <?php // echo $form->field($model, 'sssccd_min_CGPA') ?>

    <?php // echo $form->field($model, 'sssccd_remark') ?>

    <?php // echo $form->field($model, 'sssccd_created_by') ?>

    <?php // echo $form->field($model, 'sssccd_created_at') ?>

    <?php // echo $form->field($model, 'sssccd_updated_at') ?>

    <?php // echo $form->field($model, 'sssccd_updated_by') ?>

    <?php // echo $form->field($model, 'sssccd_max_SGPA') ?>

    <?php // echo $form->field($model, 'sssccd_min_SGPA') ?>

    <?php // echo $form->field($model, 'sssccd_clslvl_relId') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
