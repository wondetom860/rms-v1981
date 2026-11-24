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

    <?php $form = ActiveForm::begin(); $readOnly = !is_null($model->std_user_relId); ?>
    <div class="row">
        <div class="col-md-6" id="basic_stident_info">
            <div class="panel panel-success">
                <div class="panel-heading">Student basic information</div>
                <div class="panel-body">

                    <?= $form->field($userModel, 'first_name')->textInput(['maxlength' => true,'readonly'=>$readOnly]) ?>

                    <?= $form->field($userModel, 'middle_name')->textInput(['readonly'=>TRUE,'readonly'=>$readOnly]) ?>

                    <?= $form->field($userModel, 'last_name')->textInput(['readonly'=>TRUE,'readonly'=>$readOnly]) ?>

                    <?= $form->field($userModel, 'gender')->dropDownList([ 'MALE' => 'MALE', 'FEMALE' => 'FEMALE', ], ['prompt' => ' ','value'=>'MALE','readonly'=>$readOnly]) ?>

                    <?= $form->field($userModel, 'user_type')->textInput(['maxlength' => true,'readonly'=>$readOnly]) ?>

                    <?= $form->field($userModel, 'email')->textInput(['maxlength' => true, 'readonly'=>$readOnly]) ?>

                    <?= $form->field($userModel, 'nationality')->textInput(['maxlength' => true, 'readonly'=>$readOnly]) ?>

                    <?= $form->field($userModel, 'dob')->widget(DatePicker::className(),[
                    'pluginOptions'=>[
                        'autoClose'=>true,
                        'format'=>'yyyy-m-dd',
                        'min'=>date('yyyy-m-d'),
                        ],
                    ])?>
                </div>
            </div>
        </div>
        <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Student profile completion #1
                    </div>
                    <div class="panel-body">
                       <!--  <?= $form->field($userModel, 'id')->dropDownList(
                                ArrayHelper::map(
                                        backend\models\User::find()->all(), 'id', 'first_name'
                                ),
                                 ['prompt' => 'Select Student',
                                 //'readonly'=>TRUE,
                                 'onchange'=>'',

                             ]
                        )->label('Student first Name')?> -->

                        <?= $form->field($model, 'std_acadYear_relId')->dropDownList(
                                ArrayHelper::map(
                                        backend\models\SchoolYear::find()->where([
                                            'sch_year_desc' => \Yii::$app->params['C']['Y'],
                                        ])->all(), 'id', 'sch_year_desc'), 
                                ['prompt' => 'Select Academic Year', 'readonly'=>TRUE,'title'=>'current active academic year.',]
                        )?>
                        
                        <?= $form->field($model, 'std_admission_relId')->dropDownList(
                                ArrayHelper::map(
                                        backend\models\Admission::find()->all(), 'id', 'admission_name'
                                ), ['prompt' => 'Select Admission',
                                'onchange'=>'
                                    progId = $("#student-std_prog_relId").val();
                                    $.post( "'.Yii::$app->urlManager->createUrl('student/getregid').'",
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
                        )?>

                        <?= $form->field($model, 'std_prog_relId')->dropDownList(
                                ArrayHelper::map(
                                        backend\models\Program::find()->all(), 'id', 'program_name'
                                ), 
                                ['prompt' => 'Select Program',

                                'onchange'=>'
                                    $progIDD = $(this).val();
                                    $.post( "'.Yii::$app->urlManager->createUrl('class-level/classyearlists').'",
                                        {
                                            progId:$progIDD
                                        }, 
                                        function( data ) {
                                            console.log("Class Year: Returned: "+data);
                                          $( "#student-std_clslvl_relId" ).html( data );
                                          $.post( "'.Yii::$app->urlManager->createUrl('student/getregid').'",
                                              {
                                                    progId:$progIDD,
                                                    admId:$("#student-std_admission_relId").val()
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
                        )?>

                        <?= $form->field($model, 'std_regId')->textInput(['maxlength' => true, 'readonly'=>TRUE, 'title'=>'Student\'s Reg. ID will be auto generated after you select Admission and Program.']) 
                        ?>

                        <?= $form->field($model, 'std_type')->dropDownList(
                                ['Military','Civil','Federal Police'], 
                                ['prompt' => 'Select Student type', ]
                        )?>

                        <?= $form->field($model, 'std_dept_relId')->dropDownList(
                                ArrayHelper::map(
                                        backend\models\Department::find()->all(), 'id', 'dept_name'
                                ), 
                                ['prompt' => 'Select Department',
                                'onchange'=>'
                                $.post( "'.Yii::$app->urlManager->createUrl('specialization/listspec').'",
                                    {
                                        deptId:$(this).val(),
                                        progId:$("#student-std_prog_relId").val(),
                                        clslvl:$("#student-std_clslvl_relId").val(),
                                        admId:$("#student-std_admission_relId").val()
                                    }, 
                                    function( data ) {
                                    console.log("Returned: SPECS:  "+data);
                                  $( "select#student-std_spec_relid" ).html( data );
                                });
                                ',
                            ]
                        )?>

                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Student profile comletion #2
                    </div>
                    <div class="panel-body">

                        <?= $form->field($model, 'std_spec_relId')->dropDownList(
                            [ ArrayHelper::map(backend\models\Specialization::find()->all(),'id','spec_name'),
                            ], ['prompt' => 'Select Specialization']
                    )  ?>

                        <?= $form->field($model, 'std_clslvl_relId')->dropDownList(
                            [ ArrayHelper::map(backend\models\ClassLevel::find()->all(),'id','clslvl_name'),
                            ], ['prompt' => 'Select Class Level']
                    )  ?>

                        <?= $form->field($model, 'std_group_realId')->dropDownList(
                            [ ArrayHelper::map(backend\models\StudentGroup::find()->all(),'id','group_name'),
                            ], ['prompt' => 'Select Group']
                    )  ?>

                        <?= $form->field($model, 'std_created_at')->textInput([
                            'value'=>date('Y-m-d'),
                            'readonly'=>TRUE,
                            'title'=>'Today'
                        ]) ?>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-10">
                <div class="panel panel-info">
                    <div class="panel-body">
                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Admit') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    <!-- <?= $form->field($model, 'std_user_relId')->textInput() ?>

    <?= $form->field($model, 'std_regId')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'std_dept_relId')->textInput() ?>

    <?= $form->field($model, 'std_spec_relId')->textInput() ?>

    <?= $form->field($model, 'std_clslvl_relId')->textInput() ?>

    <?= $form->field($model, 'std_prog_relId')->textInput() ?>

    <?= $form->field($model, 'std_admission_relId')->textInput() ?>

    <?= $form->field($model, 'std_acadYear_relId')->textInput() ?>

    <?= $form->field($model, 'std_group_realId')->textInput() ?>
 -->
    <?php ActiveForm::end(); ?>

</div>
