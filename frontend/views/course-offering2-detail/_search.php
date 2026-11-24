<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseOffering2DetailSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="course-offering2-detail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'coff2det_coff2_relId') ?>
        </div><div class="col-md-3">
            <?= $form->field($model, 'coff2det_cocat_relId') ?>
        </div><div class="col-md-3">
            <?= $form->field($model, 'coff2det_course_relId') ?>
        </div><div class="col-md-3">
            <?= $form->field($model, 'coff2det_inst_relId') ?>
        </div>
    </div>

    <?php // echo $form->field($model, 'coff2det_TA_relId') ?>

    <?php // echo $form->field($model, 'coff2det_created_by') ?>

    <?php // echo $form->field($model, 'coff2det_created_at') ?>

    <?php // echo $form->field($model, 'coff2det_updated_at') ?>

    <?php // echo $form->field($model, 'coff2det_updated_by') ?>

    <div class="col-md-3">
        <div class="form-group">
            <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
            <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
