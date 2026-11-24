<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseDepartmentEvaluationSchedule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ase-department-evaluation-schedule-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="hidden">
        <?= $form->field($model, 'header_relId')->textInput() ?>
        <?= $form->field($model, 'dept_relId')->textInput() ?>
    </div>

    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'criteria_bundle_relId')->dropdownList(ArrayHelper::map($model->headerRel->getCriteriaBundleModels(),'id', function($model){ return $model->code."-[".$model->evaluator_role." >>".$model->candidate_staff_role."]"; }),['prompt' => 'Select Evaluation Type']) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'startdate')->widget(DatePicker::class,[
                'pluginOptions'=>[
                    'autoClose'=>true,
                    'format'=>'dd-m-yyyy',
                ],
            ]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'enddate')->widget(DatePicker::class,[
                'pluginOptions'=>[
                    'autoClose'=>true,
                    'format'=>'dd-m-yyyy',
                ],
            ]) ?>
        </div>
        <div class="col-md-2"><br>
            <div class="form-group pull-right" style="margin-top: 7px;">
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
        
    </div>

    <?php ActiveForm::end(); ?>
    <div class="row">
        <?= $this->render('__report/existing-schedules-under-entry',['model' => $model]) ?>
    </div>
</div>
