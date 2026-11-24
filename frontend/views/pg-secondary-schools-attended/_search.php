<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\PgSecondarySchoolsAttendedSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pg-secondary-schools-attended-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'pssa_pei_relId') ?>

    <?= $form->field($model, 'pgssa_name_of_school') ?>

    <?= $form->field($model, 'pgssa_location') ?>

    <?= $form->field($model, 'pgssa_years_from') ?>

    <?php // echo $form->field($model, 'pgssa_years_to') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
