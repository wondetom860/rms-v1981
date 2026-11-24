<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\InstructorSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="instructor-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'inst_user_relId') ?>

    <?= $form->field($model, 'inst_dep_relId') ?>

    <?= $form->field($model, 'inst_qualification') ?>

    <?= $form->field($model, 'inst_acrank') ?>

    <?php // echo $form->field($model, 'inst_active') ?>

    <?php // echo $form->field($model, 'inst_created_by') ?>

    <?php // echo $form->field($model, 'inst_created_at') ?>

    <?php // echo $form->field($model, 'inst_updated_at') ?>

    <?php // echo $form->field($model, 'inst_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
