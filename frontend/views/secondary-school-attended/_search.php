<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SecondarySchoolAttendedSeasrch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="secondary-school-attended-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'ssa_ei_relId') ?>

    <?= $form->field($model, 'ssa_school_name') ?>

    <?= $form->field($model, 'ssa_town') ?>

    <?= $form->field($model, 'ssa_year') ?>

    <?php // echo $form->field($model, 'ssa_gradeLevels') ?>

    <?php // echo $form->field($model, 'ssa_created_by') ?>

    <?php // echo $form->field($model, 'ssa_created_at') ?>

    <?php // echo $form->field($model, 'ssa_updated_by') ?>

    <?php // echo $form->field($model, 'ssa_updated_at') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
