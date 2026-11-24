<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\PgEducationInfo */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pg-education-info-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pgei_UG_university_name')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pgei_CGPA')->textInput() ?>

    <?= $form->field($model, 'pgei_field_of_study')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pgei_major')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pgei_minor')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pgei_work_experience')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pgei_reserches')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pgei_awards')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
