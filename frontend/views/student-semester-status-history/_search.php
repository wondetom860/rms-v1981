<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\StudentSemesterStatusHistorySearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="student-semester-status-history-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'sssh_std_relId') ?>

    <?= $form->field($model, 'sssh_acadYear_relId') ?>

    <?= $form->field($model, 'sssh_sem_relId') ?>

    <?= $form->field($model, 'sssh_clslvl_relId') ?>

    <?php // echo $form->field($model, 'sssh_sssn_relId') ?>

    <?php // echo $form->field($model, 'sssh_detault_sssn_relId') ?>

    <?php // echo $form->field($model, 'sssh_created_by') ?>

    <?php // echo $form->field($model, 'sssh_updated_by') ?>

    <?php // echo $form->field($model, 'sssh_created_at') ?>

    <?php // echo $form->field($model, 'sssh_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
