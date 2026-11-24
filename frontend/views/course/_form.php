<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\Course */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="course-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Course Detail
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'course_dept_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\Department::find()->all(), 'id', 'dept_name'),
                            [
                                'prompt'=>'Select Deprtment',
                                'onChange'=>' $.post( /department/deptcode",
                                    {
                                        deptId:$(this).val()
                                    }, 
                                    function( data ) {
                                    // console.log("Returned: "+data);
                                  $( "#course-course_code" ).val(data).focus();
                                });
                               // console.log("Program Change made:");',
                            ]
                        ) ?>

                        <?= $form->field($model, 'course_code')->textInput(['maxlength' => true,]) ?>

                        <?= $form->field($model, 'course_title')->textInput(['maxlength' => true]) ?>
                        
                        <?= $form->field($model, 'course_entry_type')
                            ->dropdownList(
                                [
                                    1 => 'Regular',
                                    2 => 'Transfer',
                                    3 => 'Special'
                                ],
                                [
                                    'prompt' => 'Course Entry Type',
                                ]
                            ) ?>
                    </div>
                    <?= $form->field($model, 'course_type')
                            ->dropdownList(
                                $model->getCourseTypeOptios(),
                                [
                                    'prompt' => 'Course Entry Type',
                                ]
                            ) ?>
                    </div>
                </div>
                
            </div>

            <div class="col-md-5">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Course hour detail
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'course_crdthr')->textInput() ?>

                        <?= $form->field($model, 'course_lecture_hour')->textInput() ?>

                        <?= $form->field($model, 'course_lab_hour')->textInput() ?>
                        
                        <?= $form->field($model, 'course_ETCS')->textInput() ?>

                        <?= $form->field($model, 'course_tutorial_hour')->textInput() ?>
                    </div>
                    <div class="panel-footer">
                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
                
            </div>
            
        </div>
        
    </div>

    <?php ActiveForm::end(); ?>

</div>
