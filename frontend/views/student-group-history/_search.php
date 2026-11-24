<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\StudentGroupHistorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="student-group-history-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sgh_stud_relId') ?>

    <?= $form->field($model, 'sgh_group_relId') ?>

    <?= $form->field($model, 'sgh_acadYear_relId') ?>

    <?= $form->field($model, 'sgh_semester_relId') ?>

    <?php // echo $form->field($model, 'sgh_classlevel') ?>

    <?php // echo $form->field($model, 'sgh_created_by') ?>

    <?php // echo $form->field($model, 'sgh_created_at') ?>

    <?php // echo $form->field($model, 'sgh_updated_by') ?>

    <?php // echo $form->field($model, 'sgh_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
