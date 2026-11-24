<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\PostSecondaryEducationSeasrch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="post-secondary-education-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'pse_ei_relId') ?>

    <?= $form->field($model, 'pse_institute_name') ?>

    <?= $form->field($model, 'pse_nationality_relId') ?>

    <?= $form->field($model, 'pse_from_year') ?>

    <?php // echo $form->field($model, 'pse_to_year') ?>

    <?php // echo $form->field($model, 'pse_credit_earned') ?>

    <?php // echo $form->field($model, 'pse_CGPA_obtained') ?>

    <?php // echo $form->field($model, 'pse_certificate_earned') ?>

    <?php // echo $form->field($model, 'pse_created_by') ?>

    <?php // echo $form->field($model, 'pse_updated_by') ?>

    <?php // echo $form->field($model, 'pse_created_at') ?>

    <?php // echo $form->field($model, 'pse_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
