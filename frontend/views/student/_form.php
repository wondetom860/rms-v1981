<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/* @var $model backend\models\Student */
/* @var $form yii\widgets\ActiveForm */


?>

<div class="student-form">

    <?php $form = ActiveForm::begin();
    $readOnly = !is_null($model->std_user_relId); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-success">
                    <div class="panel-heading">Student basic information</div>
                    <div class="panel-body">

                        <?= $form->field($userModel, 'first_name')->textInput(['maxlength' => true, 'readonly' => $readOnly]) ?>

                        <?= $form->field($userModel, 'middle_name')->textInput(['readonly' => $readOnly]) ?>

                        <?= $form->field($userModel, 'last_name')->textInput(['readonly' => $readOnly]) ?>

                        <?= $form->field($userModel, 'gender')->dropDownList(['MALE' => 'MALE', 'FEMALE' => 'FEMALE',], ['prompt' => ' ', 'value' => 'MALE', 'readonly' => $readOnly]) ?>

                        <?= $form->field($userModel, 'user_type')->textInput(['maxlength' => true, 'readonly' => $readOnly]) ?>

                        <?= $form->field($userModel, 'email')->textInput(['maxlength' => true, 'readonly' => $readOnly]) ?>

                        <?= $form->field($userModel, 'nationality')->textInput(['maxlength' => true, 'readonly' => $readOnly]) ?>

                        <?= $form->field($userModel, 'dob')->widget(DatePicker::class, [
                            'pluginOptions' => [
                                'autoClose' => true,
                                'format' => 'yyyy-m-dd',
                                'readonly' => $readOnly
                            ],
                        ]) ?>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Student academic profile #1
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'std_acadYear_relId')->dropDownList(
                            ArrayHelper::map(backend\models\SchoolYear::find()->all(), 'id', 'sch_year_desc'),
                            [
                                'prompt' => 'Select year of admission',
                            ]
                        )  ?>

                        <?= $form->field($model, 'std_sem_relId')->dropDownList(
                            ArrayHelper::map(backend\models\Semester::find()->all(), 'id', 'semester_name'),
                            [
                                'prompt' => 'Select Semester of admission',
                            ]
                        ) ?>

                        <?php //$form->field($model, 'std_type')->dropDownList(
                        //     ['Military' => 'Military', 'Civil' => 'Civil', 'Federal Police' => 'Federal Police', 'Foreign' => 'Foreign'],
                        //     ['prompt' => 'Select Student type',]
                        // ) 
                        ?>

                        <?php
                        //  $form->field($model, 'std_schlrshpType_relId')->dropDownList(
                        //     ArrayHelper::map(
                        //         backend\models\ScholarshipType::find()->all(),
                        //         'id',
                        //         'schType_name'
                        //     ),
                        //     ['prompt' => 'Select Scholarship Type',]
                        // ) 
                        ?>

                        <?= $form->field($model, 'std_admission_relId')->dropDownList(
                            ArrayHelper::map(
                                backend\models\Admission::find()->all(),
                                'id',
                                'admission_name'
                            ),
                            [
                                'prompt' => 'Select Admission',
                                'onchange' => '
                                    progId = $("#student-std_prog_relId").val();
                                    $.post( "' . Yii::$app->urlManager->createUrl('student/getregid') . '",
                                    {
                                        progId:progId,
                                        admId:$(this).val()
                                    }, 
                                    function( data1 ) {
                                        $("#student-std_regId").val(data1);
                                        console.log("Data from StudentController"+ data1);
                                    });
                                ',
                            ]
                        ) ?>

                        <?= $form->field($model, 'std_prog_relId')->dropDownList(
                            ArrayHelper::map(
                                backend\models\Program::find()->all(),
                                'id',
                                'program_name'
                            ),
                            [
                                'prompt' => 'Select Program',

                                'onchange' => '
                                    $progIDD = $(this).val();
                                    $.post( "' . Yii::$app->urlManager->createUrl('class-level/classyearlists') . '",
                                        {
                                            progId:$progIDD
                                        }, 
                                        function( data ) {
                                            //console.log("Class Year: Returned: "+data);
                                          $( "#student-std_clslvl_relid" ).html( data );
                                          $.post( "' . Yii::$app->urlManager->createUrl('student/getregid') . '",
                                              {
                                                    progId:$progIDD,
                                                    admId:$("#student-std_admission_relid").val()
                                                }, 
                                                function( data1 ) {
                                                    $("#student-std_regid").val(data1);
                                                    console.log("Data from StudentController"+ data1);
                                                });
                                        });
                                   // console.log("Program Change made:");
                                ',
                            ]
                            //'value'=>$regId,
                        ) ?>

                        <?= $form->field($model, 'std_dept_relId')->dropDownList(
                            ArrayHelper::map(
                                backend\models\Department::find()->all(),
                                'id',
                                'dept_name'
                            ),
                            [
                                'prompt' => 'Select Department',
                                'onchange' => '
                                $.post( "' . Yii::$app->urlManager->createUrl('specialization/listspec') . '",
                                    {
                                        deptId:$(this).val(),
                                        progId:$("#student-std_prog_relid").val(),
                                        clslvl:$("#student-std_clslvl_relid").val(),
                                        admId:$("#student-std_admission_relid").val()
                                    }, 
                                    function( data ) {
                                    console.log("Returned: SPECS:  "+data);
                                  $( "select#student-std_spec_relid" ).html( data );
                                });
                                ',
                            ]
                        ) ?>

                    </div>
                    <div class="panel-footer">
                        <div class="form-group">
                            <?= Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')? Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) : 'Changes are allowed only for Registrar' ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Student academic profile #2
                    </div>
                    <div class="panel-body">

                        <?= $form->field($model, 'std_regId')->textInput(['maxlength' => true]) ?>

                        <?php
                        $spec = !isset($model->std_dept_relId) ? backend\models\Specialization::find()->all() : backend\models\Specialization::find()->where(['spec_dep_relId' => $model->std_dept_relId, 'spec_prog_relId' => $model->std_prog_relId])->all();

                        ?>

                        <?= $form->field($model, 'entry_type')->dropDownList(
                            $model->_entry_types,
                            ['prompt' => 'Select Entry Type']
                        )  ?>

                        <?= $form->field($model, 'std_spec_relId')->dropDownList(
                            ArrayHelper::map($spec, 'id', 'spec_name'),
                            ['prompt' => 'Select Specialization']
                        )  ?>

                        <?= $form->field($model, 'std_clslvl_relId')->dropDownList(
                            [
                                ArrayHelper::map(backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
                            ],
                            ['prompt' => 'Select Class Level']
                        )  ?>

                    </div>
                    <div class="panel-footer">

                    </div>
                </div>
            </div>
        </div>

    </div>


    <?php ActiveForm::end(); ?>

</div>