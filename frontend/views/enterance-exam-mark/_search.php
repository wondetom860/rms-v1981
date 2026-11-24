<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\EnteranceExamMarkSeasrch */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="enterance-exam-mark-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index'],
        'method' => 'get',
    ]); ?>

    <?= $form->field($model, 'id') ?>

    <?= $form->field($model, 'eh_ei_relId') ?>

    <?= $form->field($model, 'eem_subject') ?>

    <?= $form->field($model, 'eem_reference_number') ?>

    <?= $form->field($model, 'eem_year') ?>

    <?php // echo $form->field($model, 'eem_markObtained') ?>

    <?php // echo $form->field($model, 'eem_created_at') ?>

    <?php // echo $form->field($model, 'eem_created_by') ?>

    <?php // echo $form->field($model, 'eem_updated_at') ?>

    <?php // echo $form->field($model, 'eem_updated_by') ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
        <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
