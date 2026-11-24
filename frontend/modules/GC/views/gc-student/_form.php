<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\GC\models\GcStudent */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="gc-student-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'entry_relId')->textInput() ?>

    <?= $form->field($model, 'graduation_mode')->textInput() ?>

    <?= $form->field($model, 'total_credit_hr')->textInput() ?>

    <?= $form->field($model, 'course_count')->textInput() ?>

    <?= $form->field($model, 'total_gps')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cgpa')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sgpa')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'major_gpa')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'last_srd2_relId')->textInput() ?>

    <?= $form->field($model, 'reg_approved')->textInput() ?>

    <?= $form->field($model, 'reg_approved_at')->textInput() ?>

    <?= $form->field($model, 'reg_approved_by')->textInput() ?>

    <?= $form->field($model, 'created_at')->textInput() ?>

    <?= $form->field($model, 'updated_at')->textInput() ?>

    <?= $form->field($model, 'deleted_at')->textInput() ?>

    <?= $form->field($model, 'deleted_by')->textInput() ?>

    <?= $form->field($model, 'created_by')->textInput() ?>

    <?= $form->field($model, 'updated_by')->textInput() ?>

    <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
