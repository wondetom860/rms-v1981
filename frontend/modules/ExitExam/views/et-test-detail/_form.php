<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\ExitExam\models\EtTestDetail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="et-test-detail-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'test_relId')->textInput() ?>

    <?= $form->field($model, 'acadYear_relId')->textInput() ?>

    <?= $form->field($model, 'mark_obtained')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pass_status')->textInput() ?>

    <?= $form->field($model, 'date_submitted')->textInput() ?>

    <?= $form->field($model, 'grade_accepted')->textInput() ?>

    <?= $form->field($model, 'grade_accepted_at')->textInput() ?>

    <?= $form->field($model, 'grade_accepted_by')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
