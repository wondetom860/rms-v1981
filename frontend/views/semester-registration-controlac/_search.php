<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterRegistrationControlacSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-registration-controlac-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'src_acadYear_relId') ?>

    <?= $form->field($model, 'src_adm_relId') ?>

    <?= $form->field($model, 'src_prog_relId') ?>

    <?= $form->field($model, 'src_sem_relId') ?>

    <?php // echo $form->field($model, 'src_clslvl_relId') ?>

    <?php // echo $form->field($model, 'src_sssn_relId') ?>

    <?php // echo $form->field($model, 'src_max_crdthr') ?>

    <?php // echo $form->field($model, 'src_min_crdthr') ?>

    <?php // echo $form->field($model, 'src_remark') ?>

    <?php // echo $form->field($model, 'src_created_by') ?>

    <?php // echo $form->field($model, 'src_created_at') ?>

    <?php // echo $form->field($model, 'src_updated_at') ?>

    <?php // echo $form->field($model, 'src_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
