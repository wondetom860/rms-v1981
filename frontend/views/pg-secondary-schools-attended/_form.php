<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\PgSecondarySchoolsAttended */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pg-secondary-schools-attended-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pssa_pei_relId')->textInput() ?>

    <?= $form->field($model, 'pgssa_name_of_school')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pgssa_location')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pgssa_years_from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pgssa_years_to')->textInput(['maxlength' => true]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
