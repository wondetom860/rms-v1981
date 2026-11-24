<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Depal\models\DepalCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="depal-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'category_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6">
            <?= $form->field($model, 'comparison_type')->dropDownList([ 'POINT_VALUE' => 'POINT VALUE', 'POINT_INTERVAL' => 'POINT INTERVAL', ], ['prompt' => '']) ?>
        </div>
        
    </div>
    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'upper_bound')->textInput() ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'attribute')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'lower_bound')->textInput() ?>
        </div>

        <div class="col-md-3">
            <div class="form-group"><br>
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success pull-right']) ?>
            </div>
        </div>
        
    </div>

    <?php ActiveForm::end(); ?>

</div>
