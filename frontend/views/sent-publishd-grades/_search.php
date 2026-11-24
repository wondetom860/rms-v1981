<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model backend\models\SentPublishdGradesSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="sent-publishd-grades-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'semreg_relId') ?>

    <?= $form->field($model, 'date_published') ?>

    <?= $form->field($model, 'recipient_email') ?>

    <?= $form->field($model, 'relation') ?>

    <?php // echo $form->field($model, 'reg_approved') ?>

    <?php // echo $form->field($model, 'mail_sent') ?>

    <?php // echo $form->field($model, 'date_sent') ?>

    <?php // echo $form->field($model, 'grade_report_path') ?>

    <?php // echo $form->field($model, 'trail_count') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
