<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SecondarySchoolAttended */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="secondary-school-attended-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'ssa_school_name')->textarea(['rows' => 1]) ?>
            </div>
            <div class="col-md-3">
                <?= $form->field($model, 'ssa_town')->textarea(['rows' => 1]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'ssa_year')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="col-md-2">
                <?= $form->field($model, 'ssa_gradeLevels')->textarea(['rows' => 1]) ?>
            </div>
            <div class="col-md-2">
                <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
            </div>
        </div>
    </div>   

    

    <?php ActiveForm::end(); ?>

</div>
