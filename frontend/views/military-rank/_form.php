<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use \yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/* @var $model backend\models\MilitaryRank */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="military-rank-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-2">
            <?= $form->field($model, 'mr_category_relId')->dropdownList(ArrayHelper::map(\backend\models\MilitaryRankCategory::find()->all(),'id','catagory_name'),['prompt' => 'Select Category']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'mr_rank_name')->textInput(['maxlength' => true]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'mr_accronym')->textInput(['maxlength' => true]) ?>
        </div>
        
        <div class="col-md-2">
            <?= $form->field($model, 'mr_order_count')->textInput() ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'mr_duration_month')->textInput() ?>
        </div>
        <div class="col-md-1 pull-right"><br>
            <div class="form-group" style="margin-top: 10px;">
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success btn-xs']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
