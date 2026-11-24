<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ScholarshipTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scholarship-type-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'schType_name') ?>

    <?= $form->field($model, 'schType_by') ?>

    <?= $form->field($model, 'schType_created_by') ?>

    <?= $form->field($model, 'schType_created_at') ?>

    <?php // echo $form->field($model, 'schType_updated_at') ?>

    <?php // echo $form->field($model, 'schType_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
