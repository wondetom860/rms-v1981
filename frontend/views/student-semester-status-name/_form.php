<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\StudentSemesterStatusName */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="student-semester-status-name-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sssn_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sssn_desc')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'sssn_pass_status')->dropdownList([
    	1=>'Pass',2=>'Further Check',3=>'Fail'
    ],['promt'=>'Select Pass Status']) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
