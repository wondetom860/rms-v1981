<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\EmploymentTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="employment-type-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'et_name') ?>

    <?= $form->field($model, 'cont_renewal_interval_month') ?>

    <?= $form->field($model, 'et_created_at') ?>

    <?= $form->field($model, 'et_created_by') ?>

    <?php // echo $form->field($model, 'et_updated_at') ?>

    <?php // echo $form->field($model, 'et_updated_by') ?>

    <?php // echo $form->field($model, 'remarks') ?>

    <?php // echo $form->field($model, 'data') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
