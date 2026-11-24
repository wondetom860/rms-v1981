<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;


/** @var yii\web\View $this */
/* @var $model backend\models\CoursePrerequisit */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="course-prerequisit-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Add new course pre-requisite detail
                    </div>
                    <div class="panel-body">
                        <div class="col-md-6">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    Set preq. for
                                </div>
                                <div class="panel-body">

                                    <?= $form->field($model, 'preq_adm_relId')->dropdownList(ArrayHelper::map(\backend\models\Admission::find()->all(),'id','admission_name'),
                                    [
                                        'prompt'=>'Select Spec.',
                                        'onChange'=>'
                                            $.post("/program/getprogs",{},function(data){
                                                 $("#courseprerequisit-preq_prog_relid").html(data);
                                                });
                                        ',
                                    ])?>

                                    <?= $form->field($model, 'preq_prog_relId')->dropdownList(
                                        ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
                                        [
                                            'prompt'=>'Select Program',
                                            'onChange'=>'$.post("/department/getdepts",{},function(data){
                                                 $("#courseprerequisit-preq_dept_relid").html(data);
                                                });'
                                        ]
                                    ) ?>

                                    <?= $form->field($model, 'preq_dept_relId')->dropdownList(
                                        ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_name')
                                        ,[
                                            'prompt'=>'Select department',
                                            'onChange'=>'
                                            console.log($(this).val());
                                            $.post(
                                                "/specialization/getspecs",
                                                {
                                                    deptId:$(this).val()
                                                    },
                                                function(data){
                                                    $("#courseprerequisit-preq_spec_relid").html(data);
                                                    // console.log(data);
                                                }
                                            );
                                            ',

                                    ]) ?>

                                    <?= $form->field($model, 'preq_spec_relId')->dropdownList(ArrayHelper::map(\backend\models\Specialization::find()->all(),'id','spec_name'),['prompt'=>'Select Spec.'])?>

                                    <?= $form->field($model, 'preq_main_course_relId')->dropdownList( 
                                        ArrayHelper::map(\backend\models\Course::find()->all(), 'id', 'course_title'),
                                        [
                                            'option'=>'Select Main Course',
                                            'onChange'=>' $.post( "'.Yii::$app->urlManager->createUrl('course/coursetitles').'",
                                                {
                                                    courseId:$(this).val()
                                                }, 
                                                function( data ) {
                                              console.log("Returned: "+data);
                                              $( "#courseprerequisit-preq_preq_course_relid" ).html(data);
                                            });
                                           // console.log("Program Change made:");',
                                        ]
                                    ) ?>
                                </div>
                            </div>
                        </div>
                        <div class="col-md-6">
                            <div class="panel panel-info">
                                <div class="panel-heading">
                                    Set preq. Course from dept.
                                </div>
                                <div class="panel-body">
                                     <?= $form->field($model, 'preqFilterDept')->dropdownList(
                                        ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_name')
                                        ,[
                                            'prompt'=>'Select department',
                                            'onChange'=>'
                                            console.log($(this).val());
                                            $.post(
                                                "/department/getdeptcourses",
                                                {
                                                    deptId:$(this).val()
                                                    },
                                                function(data){
                                                    $("#courseprerequisit-preq_preq_course_relid").html(data);
                                                    // console.log(data);
                                                }//
                                            );
                                            ',

                                    ])->label('Filter prerequisit Course by Department.') ?>

                                    <?= $form->field($model, 'preq_preq_course_relId')->dropdownList( 
                                        ArrayHelper::map(\backend\models\Course::find()->all(), 'id', 'course_title'),
                                        [
                                            'option'=>'Select preq. Course',
                                        ]
                                    ) ?>

                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="panel-footer">
                         <?= $form->field($model, 'remark')->textarea(['rows' => 3]) ?>
                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
           <!--  <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Available course pre-requsite details
                    </div>
                    <div class="panel-body">
                        <div class="container-fluid" id="CoursePrerequisitHistoryContainer">
                            
                        </div>
                    </div>
                    <div class="panel-footer">
                        <p class="bg-danger">Pre-requisit records.</p>
                    </div>
                </div>
            </div> -->
        </div>
        
    </div>

    <?php ActiveForm::end(); ?>

</div>
<!-- CoursePrerequisitHistoryContainer -->

<?php
    $this->registerJS(
        "
        // Load admission auto...
        $.post(
        '/admission/getad',
        {},
        function(data){
            $('#courseprerequisit-preq_adm_relid').html(data);
            //console.log(data);
        }
        );
        $('#CoursePrerequisitHistoryContainer').html('Loading...');
        $.post(
        '/course-prerequisit/getrecord',
        {},
        function(data){
            console.log(data);
            $('#CoursePrerequisitHistoryContainer').html(data);
        }
        );
        ",
        $this::POS_READY);
?>
