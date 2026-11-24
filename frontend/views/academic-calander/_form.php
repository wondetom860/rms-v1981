<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/* @var $model backend\models\AcademicCalander */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="academic-calander-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Academic Calendar apply for
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'acad_action_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\AcademicCalanderAction::find()->all(), 'id', 'aca_name'),
                            [
                                'prompt'=>'Select ACAD Action',
                            ]
                        ) ?>

                        <?= $form->field($model, 'acad_acadYear_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\SchoolYear::find()->all(), 'id', 'sch_year_desc'),
                            [
                                'prompt'=>'Select Academic Year',
                            ]
                        ) ?>

                        <?= $form->field($model, 'acad_adm_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\Admission::find()->all(), 'id', 'admission_name'),
                            [
                                'prompt'=>'Select Admission',
                            ]
                        ) ?>

                        <?= $form->field($model, 'acad_prog_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
                            [
                                'prompt'=>'Select Program',
                                'onChange'=>' $.post( "'.Yii::$app->urlManager->createUrl('program/clslvllistss').'",
                                                        {
                                                            progId:$(this).val()
                                                        }, 
                                                        function( data ) {
                                                      $( "#academiccalander-acad_clslvl_relid" ).html( data );
                                                    });
                                                    ',
                            ]
                        ) ?>

                        <?= $form->field($model, 'acad_clslvl_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
                            [
                                'prompt'=>'Select Class Level',
                            ]
                        ) ?>

                        <?= $form->field($model, 'acad_sem_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\Semester::find()->all(), 'id', 'semester_name'),
                            [
                                'prompt'=>'Select Semester',
                            ]
                        ) ?>
                    </div>
                    <div class="panel-footer">

                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Academic calendar setup
                    </div>
                    <div class="panel-body">

                        <?= $form->field($model, 'acad_starttime')->widget(DatePicker::className(),[
                            'pluginOptions'=>[
                                'autoClose'=>true,
                                'format'=>'yyyy-m-dd',
                            ],
                    ]) ?>

                        <?= $form->field($model, 'acad_finishtime')->widget(DatePicker::className(),[
                            'pluginOptions'=>[
                                'autoClose'=>true,
                                'format'=>'yyyy-m-dd',
                            ],
                    ]) ?>
                    </div>
                    <div class="panel-footer">
                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Save') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
