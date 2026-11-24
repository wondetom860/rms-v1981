<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\GraduationEligiblitySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="graduation-eligiblity-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ge_adm_relId') ?>

    <?= $form->field($model, 'ge_prog_relId') ?>

    <?= $form->field($model, 'ge_num_of_F') ?>

    <?= $form->field($model, 'ge_min_crdthr') ?>

    <?php // echo $form->field($model, 'ge_min_CGPA') ?>

    <?php // echo $form->field($model, 'ge_min_CGPA_major') ?>

    <?php // echo $form->field($model, 'ge_created_at') ?>

    <?php // echo $form->field($model, 'ge_created_by') ?>

    <?php // echo $form->field($model, 'ge_updated_at') ?>

    <?php // echo $form->field($model, 'ge_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
