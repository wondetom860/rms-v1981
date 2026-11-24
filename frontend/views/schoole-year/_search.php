<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SchooleYearSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="school-year-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sch_year_desc') ?>

    <?= $form->field($model, 'sch_year_status') ?>

    <?= $form->field($model, 'sch_created_by') ?>

    <?= $form->field($model, 'sch_created_at') ?>

    <?php // echo $form->field($model, 'sch_updated_by') ?>

    <?php // echo $form->field($model, 'sch_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
