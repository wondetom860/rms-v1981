<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseCatalogSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="course-catalog-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cocat_admission_relId') ?>

    <?= $form->field($model, 'cocat_program_relId') ?>

    <?= $form->field($model, 'cocat_dept_relId') ?>

    <?= $form->field($model, 'cocat_clslvl_relId') ?>

    <?php // echo $form->field($model, 'cocat_spec_relId') ?>

    <?php // echo $form->field($model, 'cocat_sem_relId') ?>

    <?php // echo $form->field($model, 'cocat_created_by') ?>

    <?php // echo $form->field($model, 'cocat_created_at') ?>

    <?php // echo $form->field($model, 'cocat_updated_by') ?>

    <?php // echo $form->field($model, 'cocat_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
