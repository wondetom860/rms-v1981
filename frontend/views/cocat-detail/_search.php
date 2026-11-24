<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\CocatDetailSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cocat-detail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cocatDet_cocat_relId') ?>

    <?= $form->field($model, 'cocatDet_course_relId') ?>

    <?= $form->field($model, 'cocatDet_created_by') ?>

    <?= $form->field($model, 'cocatDet_created_at') ?>

    <?php // echo $form->field($model, 'cocatDet_updated_by') ?>

    <?php // echo $form->field($model, 'cocat|Det_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
