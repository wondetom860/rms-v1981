<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\AddressInfoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="address-info-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'address_country_relId') ?>

    <?= $form->field($model, 'address_region_relId') ?>

    <?= $form->field($model, 'address_zone') ?>

    <?= $form->field($model, 'address_woreda') ?>

    <?php // echo $form->field($model, 'address_town') ?>

    <?php // echo $form->field($model, 'address_kebele') ?>

    <?php // echo $form->field($model, 'address_hourse_no') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
