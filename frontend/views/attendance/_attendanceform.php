<?php
/** @var yii\web\View $this */
use yii\helpers\Html;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;
use yii\helpers\ArrayHelper;
use backend\models\Course;
use frontend\models\WGroup;
use dosamigos\datepicker\DatePicker;

$this->title = 'Attendance Form';
?>

<div class="container-fluid">
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Your Courses for this semester.
                </div>
                <div class="panel-body">
                    <div class="container-fluid" id="courseListContainer">
                        Loading....
                    </div>
                </div>
                <div class="panel-footer">
                    
                </div>
            </div>
        </div>
        <div class="col-md-6" id="studentListContainer" style="display: none;">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Students who enrolled this course in this semester
                </div>
                <div class="panel-body">
                    <div class="container-fluid">
                        <div class="row form-group">
                            <div class="col-md-6">
                                <label for="attendanceDate">Date</label>
                                <input type="date" class="form-control" id="attendanceDate" />
                            </div>
                            <div class="col-md-6">
                                <label for="attendanceHour">Class Hour</label>
                                <input type="Number" class="form-control" id="attendanceHour" max="3" min="1" required='true' />
                            </div>
                        </div>
                    </div>
                    <div class="container-fluid" id="enrStdsListContainer">
                        Loading....
                    </div>
                    <div class="container-fluid bg-info">
                        <div class="row">
                            <div class="col-md-9">
                                <span id="presentListInfo"></span>                
                            </div>
                            <div class="col-md-3">
                                <input type="button" value=" Save" onclick="saveRecord();return false;" id="saveList" class="btn btn-success btn-sm" style="float: right;" /> 
                            </div>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- courseListContainer -->
<!-- Enrolled Students list enrStdsListContainer -->
<!-- studentListContainer   Bid DIV-->


<?=
$this->registerJs(
    "
    //  $('#saveList').click(function(e){
    //     e.preventDefault();
    //     saveRecord();
    // });

    total = 0;

    window.selectAll = function(){
        total = $('.selectionBox').length;
        if($('.selectionBox').length){
            $.each($('.selectionBox'),function(c){
                id = this['id'];
                status = addtoList(Number(id));
                $('#'+id).prop({'checked':(status == 1)});
            });
        }
    }

    window.saveRecord = function(){
        stdList = JSON.stringify(presentList); //JSON encode.
        // console.log(stdList);
        //CID=$('#attendance-a_course_relid').val();
        //GID=$('#attendance-a_group_relid').val();
        GDate=$('#attendanceDate').val();
        aHour=$('#attendanceHour').val();
        invalidHour = (Number($('#attendanceHour').attr('max')) < aHour) || (aHour < 1);
        if(invalidHour || GDate == '' || GDate == ' ' || GDate == 'undefined' || aHour == '' || aHour == 'undefined'){
            alert('Please Check date and/ hour.');
            $('#attendanceDate').focus();
        }else{
           if(confirm('Submit attendance form?')){
            $.post(
                '/attendance/create-attendance',
                {
                    courseId:courseID,
                    groupId :groupID ,
                    date:GDate,
                    aHour:aHour,
                    coffdetID:coffdetID,
                    studentsList:stdList
                }
                ).done(function(data){
                    console.log(data);
            });
           }
        }
        // alert(CID);
        return false;
    }


    presentList = [];
    courseID=null;
    groupID=null;
    coffdetID = null;
    date=null;
    window.addtoList=function(studentId){
        // console.log(studentId);
        r = 0;
        if(jQuery.inArray(studentId,presentList) !== -1){ // if item is already selected, remove it because user deselected it.
            presentList.splice($.inArray(studentId,presentList),1);
            r= 0;
        }else{//going to add the item to the list.
            presentList.push(studentId);
            r= 1;
        }
        $('#presentListInfo').html(presentList.length +'/'+$('.selectionBox').length+' Present.');
        console.log(presentList);
        return r;
    }

    window.loadStudent  = function(coffdetId,courseId,groupId){
        $('#presentListInfo').html('');
        presentList = [];
        total = 0;
        $('#studentListContainer').show();
        $('#enrStdsListContainer').html('Loading students...');
        courseID=courseId;
        groupID=groupId;
        coffdetID = coffdetId;
        $.post('/attendance/enrstudlists',{instId:coffdetId,courseId:courseId},function(data){
            $('#enrStdsListContainer').html(data);
            });
        //total = $('#totalList').data('totalList');
        //selected = $('#totalList').data('selected');
        //console.log(instId, courseId);
    }


    // console.log('Generated Url: '+url);
    $.post('/attendance/course-list',{},function(data){
        //console.log(data);
        $('#courseListContainer').html(data);
        });
    ",
    $this::POS_READY
);
?>
