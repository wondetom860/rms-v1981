<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\GC\models\GcSpecialMentionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gc-special-mention-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'gsm_maxCGPA') ?>

    <?= $form->field($model, 'gsm_minCGPA') ?>

    <?= $form->field($model, 'gsm_name') ?>

    <?= $form->field($model, 'gsm_created_by') ?>

    <?php // echo $form->field($model, 'gsm_created_at') ?>

    <?php // echo $form->field($model, 'gsm_updated_at') ?>

    <?php // echo $form->field($model, 'gsm_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
