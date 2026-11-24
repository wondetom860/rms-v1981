<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ThesisTitle */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="thesis-title-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <?= $model->ttSrd2Rel->srd2SemregRel->getAcadYearHeader() ?>
        </div>
        <div class="col-md-6">
            <?= $model->ttStdRel->getFullName() ?>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12">
            <?= $form->field($model, 'tt_title')->textarea(['rows' => 2,'value' => file_get_contents('uploads/thesisTitles/'.$model->tt_title)]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group pull-right">
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
