<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\StudentSemesterStatusHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="student-semester-status-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sssh_std_relId')->textInput() ?>

    <?= $form->field($model, 'sssh_acadYear_relId')->textInput() ?>

    <?= $form->field($model, 'sssh_sem_relId')->textInput() ?>

    <?= $form->field($model, 'sssh_clslvl_relId')->textInput() ?>

    <?= $form->field($model, 'sssh_sssn_relId')->textInput() ?>

    <?= $form->field($model, 'sssh_detault_sssn_relId')->textInput() ?>

    <?= $form->field($model, 'sssh_created_by')->textInput() ?>

    <?= $form->field($model, 'sssh_updated_by')->textInput() ?>

    <?= $form->field($model, 'sssh_created_at')->textInput() ?>

    <?= $form->field($model, 'sssh_updated_at')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
