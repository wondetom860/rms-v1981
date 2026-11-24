<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ProgramSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="program-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'program_adm_relId') ?>

    <?= $form->field($model, 'program_name') ?>

    <?= $form->field($model, 'program_status') ?>

    <?= $form->field($model, 'program_desc') ?>

    <?php // echo $form->field($model, 'program_created_by') ?>

    <?php // echo $form->field($model, 'program_created_at') ?>

    <?php // echo $form->field($model, 'program_updated_by') ?>

    <?php // echo $form->field($model, 'program_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
