<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\EmergencyContactSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="emergency-contact-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ec_first_name') ?>

    <?= $form->field($model, 'ec_last_name') ?>

    <?= $form->field($model, 'ec_relationShip') ?>

    <?= $form->field($model, 'ec_phone_home') ?>

    <?php // echo $form->field($model, 'ec_phone_mobile') ?>

    <?php // echo $form->field($model, 'ec_address_relId') ?>

    <?php // echo $form->field($model, 'ec_email') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
