<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WSemesterEvaluation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wsemester-evaluation-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'se_eet_relId')->textInput() ?>

    <?= $form->field($model, 'se_reviewee_relId')->textInput() ?>

    <?= $form->field($model, 'se_total_point_given')->textInput() ?>

    <?= $form->field($model, 'se_date_started')->textInput() ?>

    <?= $form->field($model, 'se_acadYear_relId')->textInput() ?>

    <?= $form->field($model, 'se_sem_relId')->textInput() ?>

    <?= $form->field($model, 'se_coff2det_relId')->textInput() ?>

    <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decris', 'Create') : Yii::t('decris', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
