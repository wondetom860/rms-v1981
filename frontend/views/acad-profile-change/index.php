<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\PaymentReasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Student Academic Profile Change requests');
$this->params['breadcrumbs'][] = $this->title;
?>
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <div class="container-fluid" class="form-group col-md-4">
                        <h5>Change Student Academic Profile</h5>
                    </div>
                </div>
                <div class="panel-body">
                    <div class="container-fluid" id="resultContainer" style="display: none;">
                        <div class="container-fluid" id="resultContainer_2">
                            
                        </div>
                    </div><hr>
                    <div class="container-fluid" id="approveReportContainer" style="display: none;">
                        
                    </div>
                </div>
                <div class="panel-footer">
                    <span class="bg-warning" style="display: none" id="updateNotification"></span>
                </div>
            </div>
        </div>
    </div>
</div>
<!-- studentSearchResultContainer -->
<!-- updateNotification -->
<!-- resultContainer_2 -->


<?php
 Modal::begin([
    'header'=>'<h4 id="modalHeader"> Change student&apos;s academic Profile. </h4>',
    'id'=>'modal',
    'size'=>'modal-lg',
 ]);
 echo "<div id='modalContent' class='container-fluid'>Loading student information 1...</div>";
 Modal::end();
 ?>


<?php

$this->registerJs(
    "

    window.showMore = function(id){
        $('#modalHeader').html('More about Student academic profile change request.');
        $('#modalHeader').parent().css('background-color','#ACE');
        $('#modal').modal('show').css('text-align','left');
        $.post('/acad-profile-change/show-more',{id:id}).done(function(data){
            $('#modalContent').html(data); 
        });
    }

    window.changeStatus = function(status,reqid){
        console.log(status,reqid);
        status = status == 0?2:status;
        $.post('/acad-profile-change/change-status',{status:status,reqid:reqid}).done(function(data){
            if(data == 1){
                loadStatusChangeRequests();
                loadApproveStatus();
            }else{
                $('#updateNotification').html(data).show();
            }
            // console.log(data);
        });
    }
    window.save12 = function(changed, stdId, prev){
        new2 = $('#new_dept_select :selected').val();
        if(typeof prev === 'undefined'){prev = '0'}
        if(typeof new2 === 'undefined'){new2 = '0'}
        console.log(changed, stdId, prev, new2);
        $.post('/acad-profile-change/create',{
            stdId:stdId,
            changed:changed,
            prev:prev,
            new2:new2
            }).done(function(data){
                $('#message_port').html(data);
        });
    }

    window.change = function(stdId,changed){
        console.log(stdId,changed);

        $('#modal').modal('show').css('text-align','left');

        $.post('/acad-profile-change/change-it',{stdId:stdId,changed:changed}).done(function(data){
            $('#modalContent').html(data);
        });
        $('#modalHeader').parent().css('background-color','#ACE');
    }


    window.changeBack = function(recordId){
        if(confirm('Are you sure to change the student status back?')){
            $.post('/acad-profile-change/change-student-semester-status-back',
            {
                recordId:recordId
            }).done(function(data){
                if(data == 1){
                    loadStatusChangeRequests();
                    loadApproveStatus();
                }else{
                   $('#updateNotification').html(data).show();
                }
            });
        }
    }


    window.changeStatus2 = function(studentId,statusId){
        
        if(confirm('Are you sure to chenge the student status?')){
            $.post('/change-student-semester-status/change-student-semester-status',{studentId:studentId,statusId:statusId}).done(function(data){
                if(data == 1){
                    loadStatusChangeStudents();
                    searchForm($('#nameSearch').val());
                }else{
                    $('#resultContainer_2').html(data).show();
                }
            })
        }
    }

    $('#nameSearch').keypress(function(e){
        if(e.which == 13){
            e.preventDefault();
            text = $(this).val();
            searchForm(text);
        }
    });

    window.searchForm = function(key){
        $('#studentSearchResultContainer').html('Getting your search...');

            $.post('/change-student-semester-status/get-student',{searchKey:key,wt:1}).done(function(data){
                $('#studentSearchResultContainer').html(data);
                $('#resultContainer').show();
            });
    }

    window.loadApproveStatus = function(){
        $.post('/acad-profile-change/get-approve-report',{}).done(function(data){
            $('#approveReportContainer').html(data).show();
        });
    }
    window.loadStatusChangeRequests = function(){
        $('#resultContainer_2').html('Loading Resulsts..');

        $.post('/acad-profile-change/get-record',{}).done(function(data){
            $('#resultContainer').show();
            $('#resultContainer_2').html(data).show();
        });
    }

    loadStatusChangeRequests();
    loadApproveStatus();
    ",
    $this::POS_READY);
?>