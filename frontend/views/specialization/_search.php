<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SpecializationSearch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="specialization-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'spec_adm_relId') ?>

    <?= $form->field($model, 'spec_prog_relId') ?>

    <?= $form->field($model, 'spec_dep_relId') ?>

    <?= $form->field($model, 'spec_clslvl_relId') ?>

    <?php // echo $form->field($model, 'spec_name') ?>

    <?php // echo $form->field($model, 'spec_desc') ?>

    <?php // echo $form->field($model, 'spec_status') ?>

    <?php // echo $form->field($model, 'spec_created_by') ?>

    <?php // echo $form->field($model, 'spec_created_at') ?>

    <?php // echo $form->field($model, 'spec_updated_at') ?>

    <?php // echo $form->field($model, 'spec_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
