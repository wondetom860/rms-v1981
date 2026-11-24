<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ProjectGroupName */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-group-name-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pg_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'pg_size')->textInput() ?>

    <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
