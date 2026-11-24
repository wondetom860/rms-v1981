<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\GcSpecialMentionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gc-special-mention-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'gsm_maxCGPA')->textInput() ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'gsm_minCGPA')->textInput() ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'gsm_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3"><br>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
