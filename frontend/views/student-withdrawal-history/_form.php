<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use backend\models\WithdrawalReason;
/** @var yii\web\View $this */
/* @var $model frontend\models\StudentWithdrawalHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="student-withdrawal-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php //echo $form->field($model, 'swh_student_relId')->textInput() ?>

    <?php //echo $form->field($model, 'swh_academicyear_relId')->textInput() ?>

    <?php //echo $form->field($model, 'swh_semester_relId')->textInput() ?>
    <div class="row">
    <div class="col-md-4">
        <?= $form->field($model, 'swh_withdrawalreason_relId')->widget(Select2::class, [
                    'data' => ArrayHelper::map(WithdrawalReason::find()->all(), 'id', 'withdrawalres_description'),
                    'options' => ['placeholder' => 'Select reason ...'],
                    //'disabled'=>'true',
                    'pluginOptions' => [
                        'allowClear' => true
                    ],
                ]); ?>  
    </div>
    </div>
    
    <?php //echo $form->field($model, 'swh_request_status')->textInput() ?>

    <?php //echo $form->field($model, 'swh_comment')->textarea(['rows' => 6]) ?>

    <?php //echo $form->field($model, 'swh_createdBy')->textInput() ?>

    <?php //echo $form->field($model, 'swh_updatedBy')->textInput() ?>

    <?php //echo $form->field($model, 'swh_createdAt')->textInput() ?>

    <?php //echo $form->field($model, 'swh_updatedAt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
