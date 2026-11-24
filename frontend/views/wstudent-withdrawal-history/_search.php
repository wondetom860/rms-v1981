<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\WStudentWithdrawalHistorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wstudent-withdrawal-history-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'swh_acadYear_relId') ?>

    <?= $form->field($model, 'swh_sem_relId') ?>

    <?= $form->field($model, 'swh_std_relId') ?>

    <?= $form->field($model, 'swh_reason_relId') ?>

    <?php // echo $form->field($model, 'swh_course_removed') ?>

    <?php // echo $form->field($model, 'swh_approved') ?>

    <?php // echo $form->field($model, 'swh_approved_by') ?>

    <?php // echo $form->field($model, 'swh_approved_at') ?>

    <?php // echo $form->field($model, 'swh_created_at') ?>

    <?php // echo $form->field($model, 'swh_created_by') ?>

    <?php // echo $form->field($model, 'swh_updated_at') ?>

    <?php // echo $form->field($model, 'swh_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
