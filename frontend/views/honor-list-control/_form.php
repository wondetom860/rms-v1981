<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\HonorListControl */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="honor-list-control-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-2"><?= $form->field($model, 'hl_acadYear_relId')->dropdownList(\yii\helpers\ArrayHelper::map(\backend\models\SchoolYear::find()->all(),'id','sch_year_desc'),['prompt'=>'Select academic Year']) ?></div>
            <div class="col-md-2"><?= $form->field($model, 'hl_sem_relId')->dropdownList(\yii\helpers\ArrayHelper::map(\backend\models\Semester::find()->all(),'id','semester_name'),['prompt'=>'Select Semester']) ?></div>
            <div class="col-md-2"><?= $form->field($model, 'hl_minSGPA')->textInput() ?></div>
            <div class="col-md-4"><?= $form->field($model, 'hl_maxSGPA')->textInput() ?></div>
            <div class="col-md-2"><br>
                <div class="form-group" style="margin-top: 5px;">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

