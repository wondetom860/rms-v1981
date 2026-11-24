<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;


/** @var yii\web\View $this */
/* @var $model frontend\modules\AcademicCalendar\models\AcProgramSchedule */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ac-program-schedule-form" style="display:<?= $model->id ? "block":'none' ?>;">

    <?php $form = ActiveForm::begin(['action' => $model->id ? '/AcademicCalendar/ac-program-schedule/update?id='.$model->id : '/AcademicCalendar/ac-program-schedule/create2']); ?>
    <br>
    <div class="row" style="background-color:#DEF;">
        <center><h4><?= "Academic Year".$model->activeProgramRel->academicYearRel->getAcHeader().", [<i>{$model->activeProgramRel->academicYearRel->getSchedule()}</i>]" ?></h4></center><br>
        <div class="col-md-12 hidden">
            <?= $form->field($model, 'active_program_relId')->textarea(['rows' => 1]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'action_relId')->dropdownList(ArrayHelper::map(\backend\models\AcademicCalanderAction::find()->all(),'id','aca_name'),
                [
                    'prompt' => '',
                    'onChange' => '
                        // when change on activity is there, reload the contents of the programs as scheduled or not.

                        reloadPrograms($(this).val());

                    ',
                ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'start_date')->widget(DatePicker::className(),[
                'pluginOptions'=>[
                    'autoClose'=>true,
                    'format'=>'dd-m-yyyy',
                ],
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'end_date')->widget(DatePicker::className(),[
                'pluginOptions'=>[
                    'autoClose'=>true,
                    'format'=>'dd-m-yyyy',
                ],
            ]) ?>
        </div>
        <div class="col-md-10">
            <?= $form->field($model, 'remark')->textarea(['rows' => 2]) ?>
        </div>
        <div class="col-md-2">
            <div class="form-group"><br><br>
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success pull-left']) ?>
            </div>
        </div>
        
    </div>

    <?php ActiveForm::end(); ?>

</div>
