<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ContAssessCatagory2Search */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cont-assess-catagory2-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cat_coff_relId') ?>

    <?= $form->field($model, 'cat_catagory_relId') ?>

    <?= $form->field($model, 'cat_maxPoint') ?>

    <?= $form->field($model, 'cat_created_at') ?>

    <?php // echo $form->field($model, 'cat_created_by') ?>

    <?php // echo $form->field($model, 'cat_updated_by') ?>

    <?php // echo $form->field($model, 'cat_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
