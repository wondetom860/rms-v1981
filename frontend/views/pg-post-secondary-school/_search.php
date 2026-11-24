<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\PgPostSecondarySchoolSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="pg-post-secondary-school-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ppss_pei_relId') ?>

    <?= $form->field($model, 'pgpss_institute_name') ?>

    <?= $form->field($model, 'pgpss_location') ?>

    <?= $form->field($model, 'pgpss_deploma_awarded') ?>

    <?php // echo $form->field($model, 'pgpss_date_of_award') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
