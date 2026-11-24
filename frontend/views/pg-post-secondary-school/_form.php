<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\PgPostSecondarySchool */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pg-post-secondary-school-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ppss_pei_relId')->textInput() ?>

    <?= $form->field($model, 'pgpss_institute_name')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pgpss_location')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pgpss_deploma_awarded')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pgpss_date_of_award')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
