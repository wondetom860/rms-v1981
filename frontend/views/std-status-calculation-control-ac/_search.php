<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\StdStatusCalculationControlAcSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="std-status-calculation-control-ac-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sssccAC_acadYear_relId') ?>

    <?= $form->field($model, 'sssccAC_Fs') ?>

    <?= $form->field($model, 'sssccAC_sem_relId') ?>

    <?= $form->field($model, 'sssccAC_adm_relId') ?>

    <?php // echo $form->field($model, 'sssccAC_prog_relId') ?>

    <?php // echo $form->field($model, 'sssccAC_sssn_relId') ?>

    <?php // echo $form->field($model, 'sssccAC_max_CGPA') ?>

    <?php // echo $form->field($model, 'sssccAC_min_CGPA') ?>

    <?php // echo $form->field($model, 'sssccAC_max_SGPA') ?>

    <?php // echo $form->field($model, 'sssccAC_min_SGPA') ?>

    <?php // echo $form->field($model, 'sssccAC_clslvl_relId') ?>

    <?php // echo $form->field($model, 'sssccAC_remark') ?>

    <?php // echo $form->field($model, 'sssccAC_created_by') ?>

    <?php // echo $form->field($model, 'sssccAC_created_at') ?>

    <?php // echo $form->field($model, 'sssccAC_updated_at') ?>

    <?php // echo $form->field($model, 'sssccAC_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
