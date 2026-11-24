<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
use kartik\date\DatePicker; 

use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\ScholarshipTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Withdrawal Application From');
$this->params['breadcrumbs'][] = $this->title;
?><br>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading" style="background-color: #00CF39;">
                    Withdrawal Application form  <B>By Student</B>
                </div>
                <div class="panel-body" id="printable">
                    <div class="container-fluid" id="requestFormCaontainer">
                        <div class="col-md-12">
                            <?php if(Yii::$app->session->hasFlash('message')){ ?>
                                <div class="alert alert-warning  alert-dismissable">
                                    <?= Yii::$app->session->getFlash('message'); ?>
                                </div>
                            <?php }?>
                        </div>
                    </div>
                    <div class="col-md-12" style="border-left: 1px solid black;">
                        <div class="container-fluid" id="requestFormCaontainerMain" style="">
                            <div class="wstudent-withdrawal-history-form">
                                <div class="row">
                                    <div class="col-md-12">
                                        <div class="row">
                                            <div class="col-md-12">
                                                <center>
                                                    <?=  strtoupper(Yii::$app->params['college'] ) ?><br>
                                                    OFFICE OF THE REGISTRAR<br>
                                                    <u>Withdrawal Form</u><br>
                                                </center>
                                            </div>
                                        </div>
                                        <div class="row">
                                            <center>
                                                <div class="col-md-4">P.O.Box 1041</div>
                                                <div class="col-md-4">Tel. 011-4-337859</div>
                                                <div class="col-md-4">011-4-338400</div>
                                            </center>
                                        </div>
                                    </div>
                                    <div class="col-md-12">
                                        <p>
                                            <u><b>Purpose</b></u>: Request for re-admission into the college will be considered only if the proper termination is certified by the registrar.
                                        </p>
                                    </div>
                                    <div class="col-md-12">
                                        <p>
                                            <u><b>Procedures</b></u>: 
                                            <ul>
                                                <li>Fill out information required</li>
                                                <li>Obtain the comment from the head of the department / head of Student affairs' office.</li>
                                                <li>Return the form th the office of the registrar with in two days of your collection</li>
                                            </ul>
                                        </p>
                                    </div>
                                    <div class="col-md-12">
                                        <p>
                                            <u><b>Part I </b></u>: <i>To be filled by the student</i>
                                        </p>
                                    </div>
                                    <div class="col-md-12">
                                        <center>
                                            <div class="container-fluid" style="text-align: left; background-color: #0FFFFF00">
                                                Full Name <u><?= $model->swhStdRel->stdUserRel->first_name.' '. $model->swhStdRel->stdUserRel->middle_name .' '. $model->swhStdRel->stdUserRel->last_name?></u><br>

                                                Reg. No: <u><?= $model->swhStdRel->std_regId ?></u><br>

                                                Admission Type: <u><?= $model->swhStdRel->stdAdmissionRel->admission_name ?></u><br>

                                                Program: <u><?= $model->swhStdRel->stdProgRel->program_name ?></u><br>

                                                Department: <u><?= $model->swhStdRel->stdDeptRel->dept_name ?></u><br>

                                                Specialization: <u><?= isset($model->swhStdRel->stdSpecRel)?$model->swhStdRel->stdSpecRel->spec_name:' - ' ?></u><br>

                                                Reason for Withdrawal: <u><?= $model->swhReasonRel->withdrawalres_description ?></u><br><br>

                                                <?php
                                                if (Yii::$app->user->can('Registrar') || Yii::$app->user->can('Admin')) {
                                                    $rUser = \common\models\User1::findOne(Yii::$app->user->identity->id);
                                                    $dgnn = "";
                                                    if (!is_null($model->swh_HOD_approved_by)) {
                                                        $dUser = \common\models\User1::findOne($model->swh_HOD_approved_by);
                                                        $dgnn = $dUser->first_name . ' '. $dUser->middle_name;
                                                    }else{
                                                        $dgnn = "Uknown";
                                                        $dept = $model->swhStdRel->std_dept_relId;
                                                        $hod = \backend\models\DepartmentHead::findOne(['dh_dept_relId'=>$dept,'dh_status'=>1]);
                                                        if ($hod) {
                                                            $dUser = $hod->dhInstRel->instUserRel;
                                                            $dgnn = $dUser->first_name . ' '. $dUser->middle_name;
                                                        }
                                                    }
                                                    
                                                ?>
                                                Department Head: <u><?= $dgnn ?></u><br>
                                                Signature: <u>______________________</u><br><br>

                                                Student Affairs' Comment (if Any): 
                                                <u>________________________________</u><br>
                                                Name: <u>________________________________</u><br>
                                                Signature: <u>______________________</u><br><br>

                                                Registrar: <u><?= $rUser->first_name . ' '. $rUser->middle_name?></u><br>
                                                Signature: <u>______________________</u><br><br>
                                                Date Recieved: <u><?= date_format(date_create($model->swh_created_at),"d M, Y") ?></u><br>
                                                <br>
                                                <button class="btn btn-primary btn-md non-print pull-right non-print" onclick="ApproveReg(<?= $model->id ?>)">
                                                    Print
                                                </button>
                                                <?php }else if(Yii::$app->user->can('Student') && is_null($model->swh_last_dateAttended)){ ?>
                                                    <label for="lastDateAttended">Last date class attended</label>
                                                    <input type="date" class="form-control" name="lastDateAttended" id="lastDateAttended">
                                                    <br>
                                                    <button class="btn btn-primary btn-md" onclick="requestStd(<?= $model->id ?>)">Request
                                                    </button>
                                                    <span id="requestProcessStatus"></span>
                                                <?php } ?>
                                            </div>
                                        </center>
                                    </div>
                                    <div class="col-md-12">
                                        <p class="bg-default">
                                            NB: This form should be filled in two copies. the 1<sup>st</sup> copy will be given to the students 2<sup>nd  </sup> will remain in the registrar`s office. In case of requesting for readmission a copy the same must be attached along with letter of application.
                                        </p>
                                        <div class="col-md-12">
                                            <hr>
                                            <center>
                                                <?php $rtrn = null; //$model->readmitSchedule($model->id,1);?>
                                                Return Back Schedule: <?= is_array($rtrn)? ("Semester <b><i>{$rtrn[0]}</i></b> of Academic Year <b><i>{$rtrn[1]}</i></b> OR <b><i>{$rtrn[2]}</i></b>"):$rtrn ?>
                                            </center>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<script type="text/javascript">
    window.ApproveReg = function(id){
        $('.non-print').hide();
        var restorePage = $('body').html();
        var printContent = $('#printable').html();
        $('body').html(printContent);
        window.print();
        $('body').html(restorePage);
    }
    window.requestStd = function(id){
        date = $('#lastDateAttended').val();
        if (date && date != 'undefined') {
            if (confirm('Continue processing?')) {
                $.post('/request-withdrawal/approve-request-student',{id:id,date:date})
                .done(function(data){
                    $('#requestProcessStatus').html(data);
                });
            }
        }else{
            alert('Please select last date of class attended.');
            $('#lastDateAttended').focus();
        }
        
    }
</script>

<!-- requestFormCaontainer -->
<!-- btnContainer -->
<!-- withdrawalFormContainer -->
<!-- enrolledCourseContainer -->

<?php

$this->registerJs(
    "

    

//     $('#apply').click(function(event){
//         event.preventDefault();

//         console.log($('#semregEntry').data('gsetid'));
//         $('#gsetId').val($('#semregEntry').data('gsetid'));
//         $('#removedCourses').val($('#semregEntry').data('semregentry'));

//         $('#w1').submit();
//         return false;
//     });


//     window.LoatStatus = function(){
//         admId = $('#studentsemesterstatushistory-admission').val();
//         progId = $('#studentsemesterstatushistory-program').val();
//         clslvlId = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();
//         spec = $('#studentsemesterstatushistory-spec').val(); 
//         deptId = $('#studentsemesterstatushistory-department').val(); 

//         $.post('/student/get-dept-student-list',
//         {
//             admId:admId,
//             progId:progId,
//             clslvlId:clslvlId,
//             specId:spec,
//             deptId:deptId,
//             t:1,

//             }).done(function(data){
//               $('#ressult_container').html(data);
//               // $('#printIcon').show();
//         });
//     }


//     courselist = [];
//     // window.AddToList = function(srd2Id){
//     //     if(jQuery.inArray(srd2Id,courselist) !== -1){ // if item is already selected, remove it because user deselected it.
//     //         courselist.splice($.inArray(srd2Id,courselist),1);
//     //     }else{//going to add the item to the list.
//     //         courselist.push(srd2Id);
//     //     }
//     //     console.log(courselist);
//     //     $('#selectedCourseCount').text(' Selected '+courselist.length+' course(s)');
//     // }


//     window.loadEnrolledCourses = function(){
//         $('#stdId').val(stdId);
//         // acadYearId = $('#studentsemesterstatushistory-sssh_acadyear_relid').val();
//         semId = $('#studentsemesterstatushistory-sssh_sem_relid').val();
//         clslvlId = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();
//         $.post('/request-withdrawal/get-enrolled-courses',{
//             stdId:stdId,
//             semId:semId,
//             clslvlId:clslvlId,
//         }).done(function(data){
//             $('#enrolledCourseContainer').html(data).show();
//         });
//     }
//     var stdId;
//     window.loadrequestForm = function(userId){
//         // withdrawalFormContainer
//         $('#requestFormCaontainerMain').show();
//         $('#withdrawalFormContainer').html('Loading form...');
//         $('#mainFormContainer').hide();
//         semId = $('#studentsemesterstatushistory-sssh_sem_relid').val();
//         clslvlId = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();
//         $.post('/request-withdrawal/get-request-form',
//             {
//                 userId:userId,
//                 semId:semId,
//                 clslvlId:clslvlId,
//             }).done(function(data){
//             try{
//                 d = JSON.parse(data);
//                 if(d[0] == 1){
//                     stdId = d[2];
//                     $('#mainFormContainer').show();
//                     $('#withdrawalFormContainer').html(d[1]);
//                     loadEnrolledCourses(d[2]);
//                 }else{
//                     // $('#requestFormCaontainerMain').show();
//                     $('#withdrawalFormContainer').html(data);
//                 }
//             }catch(e){
//                 $('#withdrawalFormContainer').html(data);
//             }
            
//         });
//     }
//     window.searchUser = function(){
//         userId2 = $('#id_input').val();
//         if( userId2 == 'undefined' || userId2 == '' || userId2 == ' '){
//             // alert('Input empty, Please enter the one.');
//             $('#id_input').focus();
//         }else{
//             $('#ressult_container').html('Searching...');
//             $.post('/admit-student/get-user-by-id',{userId:userId2,t:1}).done(function(data){
//                 $('#ressult_container').html(data);
//             });
//         }
//     }

//     $('#btnContainer').hide();
//     $('#wstudentwithdrawalhistory-applyform').on('change',function(event){
//         if(displayed == 0){
//             $('#btnContainer').show();
//             displayed = 1;
//         }else{
//             $('#btnContainer').hide();
//             displayed = 0;
//         }
//     });

//     displayed = 0;

//     window.changeStatus = function(){
//         console.log('Changed...');
//     }

//     window.loadApplicationForm = function(){
//         $.post('/request-withdrawal/get-application-form',{}).done(function(data){
//             if(data == 1){
//                 $('#requestFormCaontainerMain').show();
//                 // loadApplicationFormReal();
//             }else{
//                 $('#requestFormCaontainer').html(data);
//             }
//         });
//     }

//     // auto load application form
//     // loadApplicationForm();
    ",
    $this::POS_READY);
?>