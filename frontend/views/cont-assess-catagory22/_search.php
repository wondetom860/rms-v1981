<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ContAssessCatagorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cont-assess-catagory-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cat_name') ?>

    <?= $form->field($model, 'cat_maxPoint') ?>

    <?= $form->field($model, 'cat_created_by') ?>

    <?= $form->field($model, 'cat_created_at') ?>

    <?php // echo $form->field($model, 'cat_updated_at') ?>

    <?php // echo $form->field($model, 'cat_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
