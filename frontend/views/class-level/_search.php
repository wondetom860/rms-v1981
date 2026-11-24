<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ClassLevelSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="class-level-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'clslvl_name') ?>

    <?= $form->field($model, 'clslvl_admission_relId') ?>

    <?= $form->field($model, 'clslvl_program_relId') ?>

 <!--    <?= $form->field($model, 'clslvl_dept_relId') ?> -->

    <?php // echo $form->field($model, 'clslvl_status') ?>

    <?php // echo $form->field($model, 'clslvl_created_by') ?>

    <?php // echo $form->field($model, 'clslvl_created_at') ?>

    <?php // echo $form->field($model, 'coff_updated_by') ?>

    <?php // echo $form->field($model, 'coff_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
