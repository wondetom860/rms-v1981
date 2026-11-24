<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\GC\models\GcStudentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gc-student-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'entry_relId') ?>

    <?= $form->field($model, 'graduation_mode') ?>

    <?= $form->field($model, 'total_credit_hr') ?>

    <?= $form->field($model, 'course_count') ?>

    <?php // echo $form->field($model, 'total_gps') ?>

    <?php // echo $form->field($model, 'cgpa') ?>

    <?php // echo $form->field($model, 'sgpa') ?>

    <?php // echo $form->field($model, 'major_gpa') ?>

    <?php // echo $form->field($model, 'last_srd2_relId') ?>

    <?php // echo $form->field($model, 'reg_approved') ?>

    <?php // echo $form->field($model, 'reg_approved_at') ?>

    <?php // echo $form->field($model, 'reg_approved_by') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'deleted_at') ?>

    <?php // echo $form->field($model, 'deleted_by') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'data') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
