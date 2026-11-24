<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\AcademicCalanderSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="academic-calander-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'acad_action_relId') ?>

    <?= $form->field($model, 'acad_acadYear_relId') ?>

    <?= $form->field($model, 'acad_adm_relId') ?>

    <?= $form->field($model, 'acad_prog_relId') ?>

    <?php // echo $form->field($model, 'acad_clslvl_relId') ?>

    <?php // echo $form->field($model, 'acad_sem_relId') ?>

    <?php // echo $form->field($model, 'acad_starttime') ?>

    <?php // echo $form->field($model, 'acad_finishtime') ?>

    <?php // echo $form->field($model, 'acad_status') ?>

    <?php // echo $form->field($model, 'acad_created_by') ?>

    <?php // echo $form->field($model, 'acad_created_at') ?>

    <?php // echo $form->field($model, 'acad_updated_at') ?>

    <?php // echo $form->field($model, 'acad_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
