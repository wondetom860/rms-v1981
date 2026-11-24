<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\PgEducationInfoSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pg-education-info-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'pgei_UG_university_name') ?>

    <?= $form->field($model, 'pgei_CGPA') ?>

    <?= $form->field($model, 'pgei_field_of_study') ?>

    <?= $form->field($model, 'pgei_major') ?>

    <?php // echo $form->field($model, 'pgei_minor') ?>

    <?php // echo $form->field($model, 'pgei_work_experience') ?>

    <?php // echo $form->field($model, 'pgei_reserches') ?>

    <?php // echo $form->field($model, 'pgei_awards') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
