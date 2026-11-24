<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\CourseAssessment\models\InstanceMarkAssignmentSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="instance-mark-assignment-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
        'options' => [
            'data-pjax' => 1
        ],
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ima_catagorySetup_relId') ?>

    <?= $form->field($model, 'ima_possiblePoint') ?>

    <?= $form->field($model, 'ima_instanceName') ?>

    <?= $form->field($model, 'ima_created_by') ?>

    <?php // echo $form->field($model, 'ima_created_at') ?>

    <?php // echo $form->field($model, 'ima_updated_by') ?>

    <?php // echo $form->field($model, 'ima_updated_at') ?>

    <?php // echo $form->field($model, 'ima_approved') ?>

    <?php // echo $form->field($model, 'ima_approved_at') ?>

    <?php // echo $form->field($model, 'ima_approved_by') ?>

    <?php // echo $form->field($model, 'ima_assessed') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('rms', 'Reset'), ['class' => 'btn btn-outline-secondary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
