<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ContactListSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="contact-list-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cl_catagory_relId') ?>

    <?= $form->field($model, 'cl_full_name') ?>

    <?= $form->field($model, 'cl_phone_office') ?>

    <?= $form->field($model, 'cl_phone_mobile') ?>

    <?php // echo $form->field($model, 'cl_phone_ext') ?>

    <?php // echo $form->field($model, 'cl_created_at') ?>

    <?php // echo $form->field($model, 'cl_updated_at') ?>

    <?php // echo $form->field($model, 'cl_created_by') ?>

    <?php // echo $form->field($model, 'cl_updated_by') ?>

    <?php // echo $form->field($model, 'data') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
