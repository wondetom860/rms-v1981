<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\MilitaryRankCategory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="military-rank-category-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $form->field($model, 'catagory_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-6 pull-right"><br>
            <div class="form-group">
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
