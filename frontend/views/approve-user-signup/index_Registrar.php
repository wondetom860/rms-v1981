<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use yii\bootstrap\Modal;
use kartik\popover\PopoverX;

/** @var yii\web\View $this */
/* @var $model backend\models\User */
/* @var $form yii\widgets\ActiveForm */

$this->title = 'Approve user signup';
?>

<div class="user-form">

    <div class="container-fluid">
        <div class="row">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Approve account sign-ups
                    <div class="container-fluid pull-right" style="position: relative; top: 0px; right: 0px;">
                        <?php
                            PopoverX::begin([
                                'header' => 'Search for similar user account',
                                'placement' => PopoverX::ALIGN_BOTTOM_RIGHT,
                                'toggleButton' => ['class'=>'fa fa-search', 'style'=>'border-radius: 50%;'],
                            ]);

                            echo "<div class='container-fluid'  style='background-color:#ace;'>
                                    <div class='row'  style='padding:0px;'>
                                        <div class='col-md-9'>
                                            <input type = 'text' id='searchKeyInput' />
                                        </div>
                                        <div class='col-md-3'>
                                            <button class='btn btn-secondary btn-xs' onclick='searchUser()'>Find</button>
                                        </div>
                                    </div>
                                </div><hr>";
                            echo "<div class='container-fluid' style='font-size:8pt; padding:0px; max-height:400px; overflow:auto;' id='user_search_result'>

                                    </div>
                            ";

                            PopoverX::end();
                        ?>
                    </div>
                </div>
                <div class="panel-body" style="font-size: 10pt;">
                    <section class="col-lg-12 connectedSortable">

                        <div class="nav-tabs-custom"><!-- .nav-tabs-custom -->
                            <!-- Tabs within a box -->
                            <ul class="nav nav-tabs pull-right bg-success">
                                <li>
                                    <a href="#emp-notice" onclick="loadInst('emp-list','emp-notice')" data-toggle="tab">Employee   
                                        <span id="countO" class="pull-right" style="border-radius: 50%; color: white; font-size: 7pt; padding: 2px; float: right; background-color: red;" ></span>
                                    </a>
                                </li>
                                <li>
                                    <a href="#all-notice" onclick="loadInst('inst-list','all-notice')" data-toggle="tab" aria-expanded="true">Instructor  
                                        <span id="countI" class="pull-right" style="border-radius: 50%; color: white; font-size: 7pt; padding: 2px; float: right; background-color: red;" ></span>
                                    </a>
                                </li>
                                <li class="active">
                                    <a href="#stu-notice" onclick="loadInst('std-list','stu-notice')" data-toggle="tab" aria-expanded="false">Student   
                                        <span id="countS" class="pull-right" style="border-radius: 50%; color: white; font-size: 7pt; padding: 2px; float: right; background-color: red;" ></span>
                                    </a>
                                </li>
                                <li class="pull-left header" style="">
                                    <i class="fa fa-account"></i>
                                    Activate user account
                                </li>
                            </ul>
                            <div class="slimScrollDiv" style="position: relative; overflow: hidden; width: auto;">
                                <div class="tab-content" style="overflow: hidden; width: auto;">
                                    <!-- Notice -->
                                    <div class="tab-pane" id="all-notice" style="padding: 8px;">
                                       
                                    </div>
                                    <div class="tab-pane  active" id="stu-notice" style="padding: 8px;">

                                        
                                    </div>
                                    <div class="tab-pane" id="emp-notice" style="padding: 8px;">

                                    </div>
                                </div>
                                <div class="slimScrollBar" style="background: rgb(0, 0, 0); width: 7px; position: absolute; top: 0px; opacity: 0.4; display: none; border-radius: 7px; z-index: 99; right: 1px; height: 300px;">
                                    
                                </div>
                                <div class="slimScrollRail" style="width: 7px; height: 100%; position: absolute; top: 0px; display: none; border-radius: 7px; background: rgb(51, 51, 51); opacity: 0.2; z-index: 90; right: 1px;">
                                    
                                </div>
                            </div> <!--  /.tab-content -->
                        </div><!-- /.nav-tabs-custom -->
                    </section>

                </div>
            </div>
        </div>
    </div>

</div>

<?php

Modal::begin([
    'header'=>'<h4 class="bg-default" id="modal_header1">Student Admission Completed!!</h4>',
    'id'=>'modal_report',
    'size'=>'modal-lg',
    'options'=>[
        'style'=>[
            'text-align'=>'left',
            'justify-content'=>'left',
            'align-content'=>'left',
        ],
    ],
 ]);
 echo "<div id='modalContent1' class=''>Loading user account form...</div>";
 echo "<div id='modalFooter1' class='container-fluid'><button class='btn btn-sm btn-warning' onclick='submitUserInfo(0)'>Cancel Admission</button></div>";
 Modal::end();
 ?>

<?php
$this->registerJs(
    "

    window.searchUser = function(){
        $('#user_search_result').html('Searching....');
        searchKey = $('#searchKeyInput').val();
        $.post('/user/get-user-exists',{key:searchKey}).done(function(data){
            $('#user_search_result').html(data);
        });
    }

    window.loadApplicationForm = function(userId){

        $.get('/admit-student/admission-form-report',{id:userId}).done(function(data){
            $('#modal_header1').html('Admission Application Form');
            $('#modal_header1').parent().css('background-color','#8D0');
            $('#modal_report').modal('show');
            $('#modal_report').css('justify-content','left');
            // $('#modal_success').attr('size') = 'modal-lg';
            $('#modalFooter1').hide();
            $('#modalContent1').html(data);
            $('#modalContent1').css('text-align','left');
        });
    }


    // auto Load user accounts
    $.post('/approve-user-signup/list-all',{}).done(function(data){
        try{
            d = JSON.parse(data);
            $('#stu-notice').html(d[0]);//Student
            $('#all-notice').html(d[1]);//Inst
            $('#emp-notice').html(d[2]);//Other

            $('#countS').html(d[3]);
            $('#countI').html(d[4]);
            $('#countO').html(d[5]);

        }catch(e){
            $('#all-notice').html(data);
        }
    });


    window.loadInst = function(action,id){
        // $.post('/approve-user-signup/'+action,{}).done(function(data){
        //     $('#'+id).html(data);
        // });
    }

    window.approveSignUp = function(userId,valid){
        if(confirm('Are you sure of approving this admission?')){
            valid = valid== 0?2:valid;
            $.post('/approve-user-signup/approve-user-signup-account',
            {
                userId:userId,
                valid:valid

            }).done(function(data){
                try{
                    d = JSON.parse(data);
                    if(d[0] == 1){
                        alert('User Name : '+d[1]);
                        // loadAdmittedStudents();
                        location.reload();
                    }else{
                        $('#selectedStudCount').html(data);
                    }
                }catch(e){
                    $('#selectedStudCount').html(data);
                }
            });
        }
        return false;
    }

    window.approveAdmission = function(stdId,valid){
        if(confirm('Are you sure of approving this admission?')){
            valid = valid== 0?2:valid;
            $.post('/approve-admission/approve-admission-reg',
            {
                stdId:stdId,
                valid:valid

            }).done(function(data){
                try{
                    d = JSON.parse(data);
                    if(d[0] == 1){
                        alert('User Name : '+d[1]);
                        // loadAdmittedStudents();
                        location.reload();
                    }else{
                        $('#selectedStudCount').html(data);
                    }
                }catch(e){
                    $('#selectedStudCount').html(data);
                }
            });
        }
    }
   
    ", $this::POS_READY);
?>
