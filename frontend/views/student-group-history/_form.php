<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\StudentGroupHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="student-group-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Assign student(s) to a group.
                    </div>
                    <div class="panel-body">
                        <!-- <?= $form->field($model, 'sgh_acadYear_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\SchoolYear::find()->all(), 'id', 'sch_year_desc'),
                            [
                                'prompt'=>'Select Academic Year',
                                'onChange'=>'coff2Load()',
                            ]
                        ) ?> -->

                        <?= $form->field($model, 'sgh_adm_relId')->radioList(
                                ArrayHelper::map(
                                        backend\models\Admission::find()->all(), 'id', 'admission_name'
                                ),[
                                    'onChange'=>'
                                        // 
                                    '

                            ])->label('Admission')?>

                        <?= $form->field($model, 'sgh_prog_relId')->radioList(
                                ArrayHelper::map(
                                        backend\models\Program::find()->all(), 'id', 'program_name'
                                ),[
                                    'onChange'=>'

                                        $progIDD = $("#studentgrouphistory-sgh_prog_relid :checked").val();
                                        $.post( "'.Yii::$app->urlManager->createUrl('class-level/classyearlists').'",
                                            {
                                                progId:$progIDD
                                            }, 
                                            function( data ) {
                                                console.log("Class Year: Returned: "+data);
                                              $("#studentgrouphistory-sgh_classlevel").html( data );
                                              });
                                    '
                            ])->label('Program')?>

                            <?= $form->field($model, 'sgh_dept_relId')->dropDownList(
                                ArrayHelper::map(
                                        backend\models\Department::find()->all(), 'id', 'dept_name'),
                                        ['prompt'=>'Select Department',
                                        'onChange'=>'
                                                $.post( "'.Yii::$app->urlManager->createUrl('specialization/listspec').'",
                                            {
                                            admId:$("#studentgrouphistory-sgh_adm_relid :checked").val(),
                                            deptId:$(this).val(),
                                            progId:$("#studentgrouphistory-sgh_prog_relid :checked").val(),
                                            clslvl:$("#studentgrouphistory-sgh_classlevel").val()
                                            }, 
                                            function( data ) {
                                            console.log("Returned: SPECS:  "+data);
                                          $( "select#studentgrouphistory-sgh_spec_relid" ).html( data );
                                        });

                                        ',
                                    ]
                                )->label('Department')?>

                        <?= $form->field($model, 'sgh_classlevel')->dropdownList(
                            ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
                            [
                                'prompt'=>'Select Class Year',
                                'onChange'=>'//coffLoad()',
                            ]
                        ) ?>

                        <?= $form->field($model, 'sgh_spec_relId')->dropDownList(
                                ArrayHelper::map(
                                        backend\models\Specialization::find()->all(), 'id', 'spec_name'
                                ),['prompt'=>'Select Specialization'])->label('Specialization')?>

                    </div>
                    <div class="panel-footer">
                        <button class="btn btn-sm" id="loadStudentList">
                            Load Student List
                        </button>
                    </div>
                </div>
            </div>
            <div class="col-md-8">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Select Student and Group
                    </div>
                    <div class="panel-body">
                        <div class="container-fluid row" style="background-color: #999">
                            <div class="col-md-4">
                                <?= $form->field($model, 'sgh_semester_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\Semester::find()->all(), 'id', 'semester_name'),
                                    [
                                        'prompt'=>'Select Semester',
                                        'onChange'=>'//coff2Load()',
                                        'value'=>Yii::$app->params['C']['S'],
                                    ]
                                ) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'sgh_acadYear_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\SchoolYear::find()->all(), 'id', 'sch_year_desc'),
                                    [
                                        'prompt'=>'Select Academic Year',
                                        'onChange'=>'//coff2Load()',
                                        'value'=>Yii::$app->params['C']['Y'],
                                    ]
                                ) ?>
                            </div>
                            <div class="col-md-4">
                                <?= $form->field($model, 'sgh_group_relId')->dropdownList(
                                    ArrayHelper::map(\backend\models\StudentGroup::find()->all(), 'id', 'group_name'),
                                    [
                                        'prompt'=>'Select Group',
                                        'onChange'=>'
                                        $("#groupAssignHistory").html("Loading Students assigned to group in this semester....");
                                            $.post("/student-group-history/history",{groupId:$(this).val()},function(data){
                                                    $("#groupAssignHistory").html(data);
                                                });
                                        ',
                                    ]
                                ) ?>
                            </div>
                        </div>
                        <hr>
                        <div class="container-fluid" id="stdListToGroup">
                            
                        </div>
                        <div style="display: none;">
                            <?= $form->field($model, 'sgh_stdIds')->textInput(['placeholder'=>'List of student here']) ?>
                        </div>
                    </div>
                    <div class="panel-footer">
                        <div class="form-group" id="assignGrpBtn" style="display: none;">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Assign group') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary', 'id'=>'assignGroupBtn']) ?>
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
                        Group Assignment history
                    </div>
                    <div class="panel-body">
                        <div class="container-fluid" id="groupAssignHistory">
                            <p class="bg-warning" style="font-style: italic; padding: 4px;">
                                If any student is assigned to the selected group in current academic year, the list will be displayed here.<br><br><br>
                            </p>
                            <!-- sgh_stdIds -->
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

</div>
<!--  -->
<?=
$this->registerJs(
    "
    $('#assignGroupBtn').click(function(e){
            e.preventDefault();

            grpId = $('#studentgrouphistory-sgh_group_relid').val();
            clslvlId = $('#studentgrouphistory-sgh_classlevel').val();
            acadYearId = $('#studentgrouphistory-sgh_acadyear_relid').val();
            semId = $('#studentgrouphistory-sgh_semester_relid').val();
            stdIds = JSON.stringify(stdList);

            $('#studentgrouphistory-sgh_stdIds').val(stdIds);
            console.log(grpId,clslvlId,acadYearId,semId,stdIds);


            $.post(
                '/student-group-history/create',
                {
                    groupId:grpId,
                    clslvlId:clslvlId,
                    acadYearId:acadYearId,
                    semId:semId,
                    stdIds:stdIds
                },
                function(data){
                    $('#groupAssignHistory').html(data);
                    },
            );
        });
    stdList = [];
    window.addStdGrp = function(id){
        // Add/remove the id from the list
        if(jQuery.inArray(id,stdList) !== -1){ // if item is already selected, remove it because user deselected it.
            stdList.splice($.inArray(id,stdList),1);
        }else{//going to add the item to the list.
            stdList.push(id);
        }

        $('#selcount').html(stdList.length);
        // console.log('Coourses List : '+courseList);
        console.log('Cocats List : '+stdList);
        if (stdList.length) {
            $('#assignGrpBtn').show();
        }else{
            $('#assignGrpBtn').hide();
        }
    }

    $('#loadStudentList').on('click',function(e){
        e.preventDefault();
        stdList  =[];
        coffLoad();

        console.log('Loading Student list for group assignment.');
    });
    window.coffLoad = function(){
        $('#stdListToGroup').html('Loading....');
        // admId = $('#coursecatalog-cocat_admission_relid').val();
        // progId = $('#coursecatalog-cocat_program_relid').val();
        // deptId = $('#coursecatalog-cocat_dept_relid').val();
        // clslvlId = $('#coursecatalog-cocat_clslvl_relid').val();
        // specId = $('#coursecatalog-cocat_spec_relid').val();
        // semId = $('#coursecatalog-cocat_sem_relid').val();

        // grpId = $('#studentgrouphistory-sgh_group_relid').val();
        // clslvlId = $('#studentgrouphistory-sgh_classlevel').val();
        // acadYearId = $('#studentgrouphistory-sgh_acadyear_relid').val();
        // semId = $('#studentgrouphistory-sgh_semester_relid').val();

        admId =$('#studentgrouphistory-sgh_adm_relid :checked').val();
        progId = $('#studentgrouphistory-sgh_prog_relid :checked').val();
        deptId = $('#studentgrouphistory-sgh_dept_relid').val();
        clslvlId = $('#studentgrouphistory-sgh_classlevel').val();
        specId = $( '#studentgrouphistory-sgh_spec_relid' ).val();
        acadYearId = $('#studentgrouphistory-sgh_acadyear_relid').val();
        semId = $( '#studentgrouphistory-sgh_semester_relid' ).val();

        console.log(admId,progId,deptId,clslvlId,specId,semId);

        $.post(
            '/student/studentlist',
            {
                admId:admId,
                progId:progId,
                deptId:deptId,
                acadYearId:acadYearId,
                clslvlId:clslvlId,
                specId:specId,
                semId:semId
            },
            function(data){
                //console.log(data);
                $('#stdListToGroup').html(data);
            }
        );
    }
    ",
    $this::POS_READY
);
?>
