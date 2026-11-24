<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
?>


<?php $form = ActiveForm::begin(); ?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Grade Submission form
                </div>
                <div class="panel-body">

                    <?=
                    $form->field($model, 'sgh_adm_relId')->radioList(
                            ArrayHelper::map(
                                    backend\models\Admission::find()->all(), 'id', 'admission_name'
                            ), [
                        'onChange' => '
                                        // 
                                    '
                    ])->label('Admission')
                    ?>

                    <?=
                    $form->field($model, 'sgh_prog_relId')->radioList(
                            ArrayHelper::map(
                                    backend\models\Program::find()->all(), 'id', 'program_name'
                            ), [
                        'onChange' => '

                                        $progIDD = $("#studentgrouphistory-sgh_prog_relid :checked").val();
                                        $.post( "' . Yii::$app->urlManager->createUrl('class-level/classyearlists') . '",
                                            {
                                                progId:$progIDD
                                            }, 
                                            function( data ) {
                                                console.log("Class Year: Returned: "+data);
                                              $("#studentgrouphistory-sgh_classlevel").html( data );
                                              });
                                    '
                    ])->label('Program')
                    ?>

                    <?=
                    $form->field($model, 'sgh_classlevel')->dropdownList(
                            ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'), [
                        'prompt' => 'Select Class Year',
                        'onChange' => '//coffLoad()',
                            ]
                    )
                    ?>

                    <?=
                    $form->field($model, 'sgh_group_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\StudentGroup::find()->all(), 'id', 'group_name'), [
                        'prompt' => 'Select Group',
                        'onChange' => '
                                $("#groupAssignHistory").html("Loading Students list in the group...");
                                    $.post("/student-group-history/gradesubmission",{groupId:$(this).val()},function(data){
                                            $("#groupAssignHistory").html(data);
                                        });
                                ',
                            ]
                    )
                    ?>
                </div>
                <div class="panel-footer">
                    <button class="btn btn-sm" id="loadStudentList">
                        Load Courses
                    </button>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Select Student Grade for the course
                </div>
                <div class="panel-body">

                    <?=
                    $form->field($semModel, 'srd2_course_relId')->dropdownList(
                            [], [
                        'prompt' => 'Select Course',
                        'onChange' => 'getEnrolledStudent($(this).val())'
                            ]
                    )->label('Semester Offered Course')
                    ?> 
                    <!--  <hr> -->


                    <?=
                    $form->field($semModel, 'srd2_grade_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\GradeScale::find()->all(), 'id', 'gs_ltr_grade'), [
                        'prompt' => 'Select Letter Grade',
                            ]
                    )->Label('Letter Grade')
                    ?>
                    <hr>
                    <!--  -->
                    <div class="container-fluid" id="stdListToGroup">

                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group" id="assignGrpBtnCont" style="display: none;">
                        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Submit Grade') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id' => 'assignGroupBtn']) ?>
                        <p class="bg-info" style="float: right;"> Selected
                            <span id="selcount"></span> <i>student(s).</i>
                        </p>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <div class="panel panel-info">
                <div class="panel-heading">
                    Submitted Grades
                </div>
                <div class="panel-body">
                    <div class="container-fluid" id="groupAssignHistory">

                    </div>
                </div>
                <div class="panel-footer">
                    <p class="bg-danger"></p>
                </div>
            </div>
        </div>
    </div>
</div>

<?php ActiveForm::end(); ?>

<!-- gradeSubmitForm -->
<?php
$this->registerJs(
        "
    stdList = [];
    courseId = '';
    $('#assignGroupBtn').click(function(e){
            e.preventDefault();
            letterGrade = $('#semesterregistration2detail-srd2_grade_relid').val();
            if(letterGrade == ''|| letterGrade == 'undefined'){
                alert('Please select Grade Letter.');
                $('#semesterregistration2detail-srd2_grade_relid').focus();
                }else{
                    if(stdList.length >0){
                        stdListS = JSON.stringify(stdList);
                        lgId = letterGrade;
                        $.post(
                        'submit-grade/create',
                        {
                            stdListS:stdListS,
                            lgId:lgId,
                            courseId:courseId
                        },
                        function(data){
                                console.log(data);
                                if(data>0){
                                        getEnrolledStudent();
                                    }else{
                                        $('#groupAssignHistory').html(data);
                                        getEnrolledStudent();
                                    }
                            });
                        }else{
                            alert('No Student selected.');
                        }
                }
        });
    window.addGrade = function(id){
        if(jQuery.inArray(id,stdList) !== -1){ 
            stdList.splice($.inArray(id,stdList),1);
        }else{
            stdList.push(id);
        }
        console.log(stdList);
        console.log('Cocats List : '+stdList);
        $('#selcount').text(stdList.length);
        if (stdList.length) {
            $('#assignGrpBtnCont').show();
        }else{
            $('#assignGrpBtnCont').hide();
        }
    }
    window.getEnrolledStudent = function(id){
        courseId = id;
        stdList = [];
        console.log(id);
        $.post('/submit-grade/getenrolledstudent',{id:id},function(data){
        console.log(data);
        $('#stdListToGroup').html(data);
        });
    }
    $.post('/submit-grade/getsemestercourses',{},function(data){
        console.log(data);
        $('#semesterregistration2detail-srd2_course_relid').html(data);
        });
    $('#loadStudentList').click(function(e){
        e.preventDefault();

        $.post('/submit-grade/getsemestercourses',{},function(data){
            console.log('Semester courses: '+data);
            });
        });
    ", $this::POS_READY
);
?>
