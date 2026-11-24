<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\EducationInformationSeasrch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="education-information-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ei_G10_doc_scan') ?>

    <?= $form->field($model, 'ei_G12_doc_scan') ?>

    <?= $form->field($model, 'ei_enteranceMarkPoint') ?>

    <?= $form->field($model, 'ei_created_at') ?>

    <?php // echo $form->field($model, 'ei_updated_at') ?>

    <?php // echo $form->field($model, 'ei_created_by') ?>

    <?php // echo $form->field($model, 'ei_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
