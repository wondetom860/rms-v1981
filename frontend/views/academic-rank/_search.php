<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\AcademicRankSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="academic-rank-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'acr_name') ?>

    <?= $form->field($model, 'acr_desc') ?>

    <?= $form->field($model, 'acr_created_by') ?>

    <?= $form->field($model, 'acr_created_at') ?>

    <?php // echo $form->field($model, 'acr_updated_by') ?>

    <?php // echo $form->field($model, 'acr_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
