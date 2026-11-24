<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\AcademicCalanderActionSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="academic-calander-action-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'aca_name') ?>

    <?= $form->field($model, 'aca_created_by') ?>

    <?= $form->field($model, 'aca_created_at') ?>

    <?= $form->field($model, 'aca_updated_by') ?>

    <?php // echo $form->field($model, 'aca_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
