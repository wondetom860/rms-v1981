<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\AdmissionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="admission-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'admission_name') ?>

    <?= $form->field($model, 'admission_status') ?>

    <?= $form->field($model, 'admission_created_by') ?>

    <?= $form->field($model, 'admission_created_at') ?>

    <?php // echo $form->field($model, 'admission_updated_by') ?>

    <?php // echo $form->field($model, 'admission_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
