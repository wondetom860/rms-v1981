<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use backend\models\WithdrawalReason;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ScholarshipTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Withdrawal');
$this->params['breadcrumbs'][] = $this->title;
?><br>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading" style="background-color: #00CF39;">
                    Diciplinary withdrawal form  <B>By Department head</B>
                </div>
                <div class="panel-body" style="font-size: 9pt;">
                    <div class="container-fluid" id="requestFormCaontainer">
                        <div class="col-md-12">
                            <?php if(Yii::$app->session->hasFlash('message')){ ?>
                                <div class="alert alert-warning  alert-dismissable">
                                    <?= Yii::$app->session->getFlash('message'); ?>
                                </div>
                            <?php }?>
                        </div>
                    </div>
                    <div class="row">
                    <?php $form = ActiveForm::begin(); ?>
                      <div class="col-md-3">
                            <?= $form->field($model, 'admission')->dropdownList(
                                ArrayHelper::map(\backend\models\Admission::find()->all(),'id','admission_name'),
                                [
                                'prompt'=>'  ',
                            ])->label('Admission') ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'program')->dropdownList(
                                ArrayHelper::map(\backend\models\Program::find()->all(),'id','program_name'),
                                [
                                'prompt'=>'  ',
                                    'onChange'=>' 
                                        $.post( "/program/clslvllists",
                                            {
                                                progId:$(this).val()
                                            }, 
                                          function( data ) {
                                              //console.log("Returned: "+data);
                                              $("#studentsemesterstatushistory-sssh_clslvl_relid" ).html( data ).triger("change");
                                              LoatStatus();       
                                            }); ',
                            ])->label('Program') ?>
                        </div>
                        <div class="col-md-3">
                        <?= $form->field($model, 'department')->dropdownList(
                            $model->getInstDept(),
                            [
                            'prompt'=>'  ',
                            'onChange'=>'$.post("/specialization/listspec-cp",
                                  {
                                    admId:$("#studentsemesterstatushistory-admission").val(),
                                    progId:$("#studentsemesterstatushistory-program").val(),
                                    deptId:$("#studentsemesterstatushistory-department").val(),
                                  }, 
                                  function( data ) {
                                      //console.log("Returned: "+data);
                                      $("#studentsemesterstatushistory-spec" ).html( data ).trigger("change");
                                  }); ',
                            ])->label('Department') ?>
                       </div>
                       <div class="col-md-3">             
                            <?= $form->field($model, 'sssh_acadYear_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\SchoolYear::find()->all(),'id','sch_year_desc'),
                                [
                                'prompt'=>'  ',
                                'onChange'=>'LoatStatus()',
                            ])->label('Acad. Year') ?>
                       </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'sssh_clslvl_relId')->dropdownList(
                              ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
                                    [
                                         'prompt'=>'  ',
                                          'onChange'=>'LoatStatus()',
                                ])->label('ClassLevel') ?>
                        </div>
                        <div class="col-md-3">             
                            <?= $form->field($model, 'sssh_sem_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\Semester::find()->all(),'id','semester_name'),
                                [
                                'prompt'=>'  ',
                                'onChange'=>'LoatStatus()',
                            ])->label('Semester') ?>
                       </div>
                       <div class="col-md-3">
                        <?= $form->field($model, 'spec')->dropdownList(
                            ArrayHelper::map(\backend\models\Specialization::find()->all(),'id','spec_name'),
                            [
                            'prompt'=>'  ',
                            'onChange'=>'LoatStatus()',
                        ])->label('Specialization') ?>
                       </div>
                       <?php ActiveForm::end(); ?>
                    </div>
                    <div>
                        <div class="col-md-4" style="border-right: 1px solid black; font-size: 9pt;">
                            <h4>Get Student</h4>
                            <div class="container-fluid">
                                <div class="col-md-10">
                                    <input type="text" onkeyup="searchUser()" class="form-control" name="" title="Search Student by first name / middle name or last name." id="id_input">
                                </div>
                                <div class="col-md-3 pull-left">
                                    <button class="btn pull-right btn-primary" onclick="searchUser();return false;">Search</button>
                                </div>
                            </div>
                            <hr>
                            <div class="container-fluid" id="ressult_container">
                                
                            </div>
                        </div>
                        <div class="col-md-8" style="border-left: 1px solid black;">
                            <div class="container-fluid" id="requestFormCaontainerMain" style="display: none;">
                                <div class="wstudent-withdrawal-history-form">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <div class="container-fluid" id="withdrawalFormContainer">
                                                
                                            </div>
                                        </div>
                                    </div>

                                    <?php $form = ActiveForm::begin(['action'=>'/w-student-withdrawal-history/create2']); ?>

                                    <div class="row" style="border-top: 0px solid black; display: none; font-size: 9pt;" id="mainFormContainer">
                                        <div class="container-fluid">
                                            <div class="panel panel-default">
                                                <div class="panel-body">
                                                    <div class="col-md-6">
                                                        <?= $form->field($modelW, 'reasonType')->widget(Select2::classname(), [
                                                                'data' => ArrayHelper::map(WithdrawalReason::find()->groupBy('withdrawalres_type')->all(), 'id', 'withdrawalres_type'),
                                                                'options' => [
                                                                    'placeholder' => 'Select withdrwaal type...',
                                                                    'onchange'=>'
                                                                        $.post("/withdrawal-reason/get-types", { 
                                                                            catagoryName:$(this).val()
                                                                            })
                                                                        .done(function(data){
                                                                            $("#wstudentwithdrawalhistory-swh_reason_relid").html(data).trigger("change");
                                                                        });
                                                                    '
                                                                ],
                                                                //'disabled'=>'true',
                                                                'pluginOptions' => [
                                                                    'allowClear' => true
                                                                ],
                                                            ]); 
                                                        ?>  
                                                    </div>
                                                    <div class="col-md-6">
                                                        <?= $form->field($modelW, 'swh_reason_relId')->dropdownList(
                                                            [],
                                                            [
                                                                'prompt'=>'Select Withdrawal reason',
                                                            ]
                                                        )
                                                        ?>  
                                                    </div>
                                                    <div class="col-md-12">
                                                        <span style="font-size: 8pt; font-style: italic; color: white; background-color: #FF0055">All NON-graded courses enrolled by the student for the semester will be changed to (F).</span>
                                                        <label for="enrolledCourseContainer">Enrolled Courses</label>
                                                        <div class="container-fluid" id="enrolledCourseContainer">
                                                            
                                                        </div>

                                                        <?php echo Html::activeHiddenInput($modelW, "removedCourseList",['id'=>'removedCourses']);?>

                                                        <?php ?>
                                                        <?php echo Html::activeHiddenInput($modelW, "swh_std_relId",['id'=>'stdId']);?>

                                                        <?php ?>
                                                        <span style="font-style: italic; padding: 4px; float: right;" class="pull-right" id="selectedCourseCount">0</span>
                                                    </div>
                                                    <div class="col-md-12">
                                                        <?= $form->field($modelW,'applyForm')->checkBox(
                                                            [
                                                                1=>'YES',
                                                                0=>'NO',
                                                            ]
                                                            )?>
                                                    </div>
                                                    <div class="col-md-12" style="display: none" id="btnContainer"><br>
                                                         <div class="form-group pull-left">
                                                            <?= Html::submitButton($modelW->isNewRecord ? Yii::t('decirs', 'Apply') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','id'=>'apply']) ?>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>

                                    <?php ActiveForm::end(); ?>

                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<!-- requestFormCaontainer -->
<!-- btnContainer -->
<!-- withdrawalFormContainer -->
<!-- enrolledCourseContainer -->

<?php

$this->registerJs(
    "

    $('#apply').click(function(event){
        event.preventDefault();

        $('#removedCourses').val(JSON.stringify(courselist));

        $('#w0').submit();
    });


    window.LoatStatus = function(){
        admId = $('#studentsemesterstatushistory-admission').val();
        progId = $('#studentsemesterstatushistory-program').val();
        clslvlId = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();
        spec = $('#studentsemesterstatushistory-spec').val(); 
        deptId = $('#studentsemesterstatushistory-department').val(); 

        $.post('/student/get-dept-student-list',
        {
            admId:admId,
            progId:progId,
            clslvlId:clslvlId,
            specId:spec,
            deptId:deptId,
            t:1,

            }).done(function(data){
              $('#ressult_container').html(data);
              // $('#printIcon').show();
        });
    }


    courselist = [];
    window.AddToList = function(srd2Id){
        if(jQuery.inArray(srd2Id,courselist) !== -1){ // if item is already selected, remove it because user deselected it.
            courselist.splice($.inArray(srd2Id,courselist),1);
        }else{//going to add the item to the list.
            courselist.push(srd2Id);
        }
        console.log(courselist);
        $('#selectedCourseCount').text(' Selected '+courselist.length+' course(s)');
    }
    window.loadEnrolledCourses = function(stdId){
        $('#stdId').val(stdId);
        $.post('/request-withdrawal/get-enrolled-courses',{stdId:stdId}).done(function(data){
            $('#enrolledCourseContainer').html(data).show();
        });
    }
    window.loadrequestForm = function(userId){
        // withdrawalFormContainer
        $('#requestFormCaontainerMain').show();
        $('#withdrawalFormContainer').html('Loading form...');
        $('#mainFormContainer').hide();
        $.post('/request-withdrawal/get-request-form',{userId:userId}).done(function(data){
            try{
                d = JSON.parse(data);
                if(d[0] == 1){
                    $('#mainFormContainer').show();
                    $('#withdrawalFormContainer').html(d[1]);
                    loadEnrolledCourses(d[2]);
                }else{
                    // $('#requestFormCaontainerMain').show();
                    $('#withdrawalFormContainer').html(data);
                }
            }catch(e){
                $('#withdrawalFormContainer').html(data);
            }
            
        });
    }
    window.searchUser = function(){
        userId2 = $('#id_input').val();
        if( userId2 == 'undefined' || userId2 == '' || userId2 == ' '){
            // alert('Input empty, Please enter the one.');
            $('#id_input').focus();
        }else{
            $('#ressult_container').html('Searching...');
            $.post('/admit-student/get-user-by-id',{userId:userId2,t:1}).done(function(data){
                $('#ressult_container').html(data);
            });
        }
    }

    $('#btnContainer').hide();
    $('#wstudentwithdrawalhistory-applyform').on('change',function(event){
        if(displayed == 0){
            $('#btnContainer').show();
            displayed = 1;
        }else{
            $('#btnContainer').hide();
            displayed = 0;
        }
    });

    displayed = 0;

    window.changeStatus = function(){
        console.log('Changed...');
    }

    window.loadApplicationForm = function(){
        $.post('/request-withdrawal/get-application-form',{}).done(function(data){
            if(data == 1){
                $('#requestFormCaontainerMain').show();
                // loadApplicationFormReal();
            }else{
                $('#requestFormCaontainer').html(data);
            }
        });
    }

    // auto load application form
    // loadApplicationForm();
    ",
    $this::POS_READY);
?>