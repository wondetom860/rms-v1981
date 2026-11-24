<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\CourseAssessment\models\ContAssessCatagorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cont-assess-catagory-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'cat_acadYear_relId') ?>

    <?= $form->field($model, 'cat_sem_relId') ?>

    <?= $form->field($model, 'cat_coff_relId') ?>

    <?= $form->field($model, 'cat_course_relId') ?>

    <?php // echo $form->field($model, 'cat_catagory_relId') ?>

    <?php // echo $form->field($model, 'cat_maxPoint') ?>

    <?php // echo $form->field($model, 'cat_created_at') ?>

    <?php // echo $form->field($model, 'cat_created_by') ?>

    <?php // echo $form->field($model, 'cat_updated_by') ?>

    <?php // echo $form->field($model, 'cat_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
