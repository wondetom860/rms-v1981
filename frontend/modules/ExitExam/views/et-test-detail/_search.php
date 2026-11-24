<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\ExitExam\models\EtTestDetailSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="et-test-detail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'test_relId') ?>

    <?= $form->field($model, 'acadYear_relId') ?>

    <?= $form->field($model, 'mark_obtained') ?>

    <?= $form->field($model, 'pass_status') ?>

    <?php // echo $form->field($model, 'date_submitted') ?>

    <?php // echo $form->field($model, 'grade_accepted') ?>

    <?php // echo $form->field($model, 'grade_accepted_at') ?>

    <?php // echo $form->field($model, 'grade_accepted_by') ?>

    <?php // echo $form->field($model, 'created_at') ?>

    <?php // echo $form->field($model, 'created_by') ?>

    <?php // echo $form->field($model, 'updated_at') ?>

    <?php // echo $form->field($model, 'updated_by') ?>

    <?php // echo $form->field($model, 'data') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
