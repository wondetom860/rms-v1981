<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\DepartmentSelection\models\DepartmentSelectEntry */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="department-select-entry-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'student_relId')->textInput() ?>

    <?= $form->field($model, 'setting_relId')->textInput() ?>

    <?= $form->field($model, 'dept_choice')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'approved_dept_relId')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
