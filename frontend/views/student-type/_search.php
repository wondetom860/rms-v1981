<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\StudentTypeSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="student-type-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'stdType_name') ?>

    <?= $form->field($model, 'stdType_desc') ?>

    <?= $form->field($model, 'stdType_created_by') ?>

    <?= $form->field($model, 'stdType_created_at') ?>

    <?php // echo $form->field($model, 'stdType_updated_by') ?>

    <?php // echo $form->field($model, 'stdType_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
