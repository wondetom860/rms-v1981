<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\InstanceMarkAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="instance-mark-assignment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ima_catagorySetup_relId') ?>

    <?= $form->field($model, 'ima_possiblePoint') ?>

    <?= $form->field($model, 'ima_instanceName') ?>

    <?= $form->field($model, 'ima_created_by') ?>

    <?php // echo $form->field($model, 'ima_created_at') ?>

    <?php // echo $form->field($model, 'ima_updated_by') ?>

    <?php // echo $form->field($model, 'ima_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
