<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\AddHistorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="add-history-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ah_srd2_relId') ?>

    <?= $form->field($model, 'ah_std_relId') ?>

    <?= $form->field($model, 'ah_course_relId') ?>

    <?= $form->field($model, 'ah_acadYear_relId') ?>

    <?php // echo $form->field($model, 'ah_sem_relId') ?>

    <?php // echo $form->field($model, 'ah_addWithGroup_relId') ?>

    <?php // echo $form->field($model, 'ah_coff_relId') ?>

    <?php // echo $form->field($model, 'ah_approved') ?>

    <?php // echo $form->field($model, 'ah_approved_by') ?>

    <?php // echo $form->field($model, 'ah_approved_at') ?>

    <?php // echo $form->field($model, 'ah_created_at') ?>

    <?php // echo $form->field($model, 'ah_created_by') ?>

    <?php // echo $form->field($model, 'ah_updated_by') ?>

    <?php // echo $form->field($model, 'ah_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
