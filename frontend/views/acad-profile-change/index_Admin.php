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

$this->title = Yii::t('decirs', 'Change Student Academic Profile');
$this->params['breadcrumbs'][] = $this->title;
?>
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    <h5>Change Student Academic Profile</h5>
                    <div class="container-fluid" class="form-group col-md-4">
                        <input type="text" name="nameSearch" title="Search for Student" placeholder="Search Student ID / Name adn hit ENTER" id="nameSearch" class="form-control" />
                    </div>
                </div>
                <div class="panel-body">
                    <div class="container-fluid" id="resultContainer" style="display: none;">
                        <h6>Search result</h6>
                        <div class="container-fluid" id="studentSearchResultContainer">
                            
                        </div>
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
    'header'=>'<h5 id="modalHeader"> Change student&apos;s academic Profile. </h5>',
    'id'=>'modal',
    'size'=>'modal-lg',
 ]);
 echo "<div id='modalContent' class='container-fluid table-responsive'>Loading student information...</div>";
 Modal::end();
 ?>


<?php

$this->registerJs(
    "

    window.deleteRequest = function(id){
        if(confirm('You want to delete request?')){
            $.post('/acad-profile-change/delete-request',{id:id}).dons(function(data){
                if(data == 1){
                    window.location.href = window.location;
                }else{
                    $('#message_port').html(data);
                }
            });
        }
    }

    window.save12 = function(changed, stdId, prev){
        new2 = $('#new_dept_select :selected').val();
        newg = $('#new_group_select :selected').val();
        prevGroup = $('#new_dept_select').data('prevgroup');

        console.log(prevGroup);

        if(typeof prev === 'undefined'){prev = '0'}
        if(typeof new2 === 'undefined'){new2 = '0'}
        if(typeof newg === 'undefined'){newg = '0'}
        if(typeof prevGroup === 'undefined'){prevGroup = '0'}
        console.log(changed, stdId, prev, new2);
        $.post('/acad-profile-change/create',{
            stdId:stdId,
            changed:changed,
            prev:prev,
            new2:new2,
            newg:newg,
            prevg:prevGroup
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

    window.cont = function(dept,adm,prog,spec,acadYear,sem,clslvl){
        // get gsets under this specification

        // $('.btn').show();
        clslvl = $('#new_dept_select').data('clslvl');//data-clslvl
        console.log(dept,adm,prog,spec,acadYear,sem,clslvl);
        prevGroup = $('#new_dept_select').data('prevgroup');

        console.log('Preg Group => '+prevGroup);


        $.post('w-group-setup/get-group-lists-peryear',
        {
            admId:adm,
            progId:prog,
            acadYearId:acadYear,
            semId:sem,
            deptId:dept,
            specId:spec,
            clslvlId:clslvl
        })
        .done(function(data){
            $('#new_group_select').html(data);
        });
    }

    window.changeStatusBack = function(recordId){
        if(confirm('Are you sure to change the student status back?')){
            $.post('/change-student-semester-status/change-student-semester-status-back',
            {
                recordId:recordId
            }).done(function(data){
                if(data == 1){
                    loadStatusChangeStudents();
                    searchForm($('#nameSearch').val());
                }else{
                    $('#resultContainer_2').html(data).show();
                }
            });
        }
    }


    window.changeStatus = function(studentId,statusId){
        
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
    window.loadStatusChangeStudents = function(){
        $('#resultContainer_2').html('Loading Resulsts..');

        $.post('/change-student-semester-status/get-record',{}).done(function(data){
            $('#resultContainer_2').html(data).show();
        });
    }

    loadStatusChangeStudents();
    ",
    $this::POS_READY);
?>