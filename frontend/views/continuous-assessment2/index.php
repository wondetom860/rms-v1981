<?php
use yii\helpers\Html;
/** @var yii\web\View $this */
// $this->title='Semester Courses Assigned.'
use yii\bootstrap\Modal;
$this->title = Yii::t('rms', 'Continuous Assessments');
$this->params['breadcrumbs'][] = $this->title;
?>
<h4><?= Html::encode($this->title) ?></h4>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-6" style="font-size: 9pt;">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Course Assignments.
                </div>
                <div class="panel-body">
                    <div class="container-fluid table-responsive" id="courseListContainer"
                        style="max-height: 400px; overflow-x: auto;">
                        Loading....
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div id="course-info-contained">

            </div>
            <div class="container-fluid" style="display: none; background-color: #ed4; padding: 5px; max-height: 380px; overflow: auto;">
                <div class="col-md-4" id="submitmarkContainer">

                </div>
                <div class="col-md-4" id="instanceContainer2">
                    <input type="text" class="form-control" id="instanceContainer" placeholder=" Assessment Instance">
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" readonly="" required="" class="form-control" min="1"
                        id="maxPointContainer" value="0" placeholder="Max possible marks">
                </div>

                <div class="col-md-1">
                    <button class="btn btn-xs btn-primary" title="Load Mark Sheet Form" onclick="loadStudent2()"
                        style="margin-top: 10px;">>></button>
                </div>
            </div><br>
            <div class="container-fluid" id="markSubmissionFormContainer"
                style="border: 1px dashed gold; padding: 5px; color: black; display: none;">

            </div><br>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" id="studentListContainer" style="display: none;">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Submitted Marks for Continuous Assessment
                </div>
                <div class="panel-body">
                    <div class="container-fluid table-responsive" id="enrStdsListContainer">
                        Loading....
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

Modal::begin([
    'header' => '<h4>Students\' mark for the selected assessment instance.</h4>',
    'id' => 'modal_stdMark',
    'size' => 'modal-lg',
]);
echo "<div id='modalContent_stdMark' class='container-fluid'>Loading student mark...</div>";
Modal::end();

?>
<!-- courseListContainer -->
<!-- Enrolled Students list enrStdsListContainer -->
<!-- studentListContainer   Bid DIV-->
 <!-- course-info-contained -->

<!-- submitmarkContainer -->


<?=
    $this->registerJs(
        "
    window.LoadModalForEdit = function(instanceId,gsetId,courseId){
        $('#modal_stdMark').modal('show').find('#modalContent_stdMark').html('Loading student mark').css({'text-align':'left'});

        $.get('/continuous-assessment2/index',{instanceId:instanceId,gsetId:gsetId,courseId:courseId}).done(function(resp){
            console.log(resp);
            $('#modalContent_stdMark').html(resp);
        });
        $('#modal_stdMark').on('hide.bs.modal',function(){
           window.location.href = window.location;
           // return false;
        });
    }
    window.dhfgfvbsfgshgfjsdfatydasdv = function(){
        studentGrades = {};
        succ = 1;
        $.each($('.__markInput'),function(){
            id = $(this).data('stdid');
            mark = $(this).val();
            max = Number($(this).attr('max'));
            console.log(mark,max);

            // return false;

            if(((max >= mark) && (mark >= 0)) || mark == ''){  //
                studentGrades[id] = mark;
                succ = 1;
            }else{
                succ = 0;
                //$(this).trigger_error('Error');
                $(this).focus().prop('title','Input invalid, Mark must be less than the maximum.' + $(this).attr('max'));
                $(this).focus();
                return false;
            }
        });

        if(succ == 1){
            studentGradesString = JSON.stringify(studentGrades);

           // console.log($('#w_t_009 :selected').val());
            if(confirm('Are you sure to submit marks?')){
                $('#markSubmissionFormContainer').hide();
               $.post(
                '/continuous-assessment2/create2',{
                    coffdetId:coffdetID,
                    courseId:courseId,
                    maxPoint:$('#maxPointContainer').val(),
                    instanceName:$('#instanceContainer2 :selected').val(),
                    catagory:$('#w_t_009 :selected').val(),
                    studentList:studentGradesString
                }).done(function(data){
                    try{
                        if(data == 1){
                            loadStudent(coffdetID,courseId);
                        }else{
                            $('#enrStdsListContainer').html(data);
                        }
                        
                    }catch(e){
                        // loadStudent(data);
                    }
            });     
            }
        }else{
            alert('Please check the mark, it is not complete');
        }
    }

    window.loadStudent2 = function(){
        catagoryId = $('#w_t_009 :selected').val();
        instanceId = $('#instanceContainer2 :selected').val();
        if($('#w_t_009 :selected').val() == '' || $('#w_t_009_instance').val() == '' || $('#maxPointContainer').val() == 0 || $('#maxPointContainer').val() < 1){
            alert('Please select assessment category and/or possible max point of the assessment.');
            $('#maxPointContainer').focus();
        }else{
            maxPoint = $('#maxPointContainer').val();
            remPoint = $('#w_t_009 :selected').data('rem');
            if(maxPoint > remPoint){
                alert('You assessment max point is more than remaining point. Please select max point less then '+remPoint);
                $('#maxPointContainer').val(remPoint-1).focus();
            }else{
                 $.post('/continuous-assessment2/enrstudlists2',
                    {
                        instId:coffdetID,
                        courseId:courseId,
                        maxPoint:maxPoint,
                        catagoryId:catagoryId,
                        instanceId:instanceId
                    },
                    function(data){
                        $('#markSubmissionFormContainer').html(data).show();
                    });
            }
            
            // console.log(coffdetID,courseId);
        }
        
    }

    var coffdetID = '';
    var courseId = '';
    var studentGradesString = null;
    window.getInstance2 = function(coffdetID2,courseId2,catagoryId){
        $('#markSubmissionFormContainer').hide();
        $('#instanceContainer2').html('...');
        $('#maxPointContainer').val(0);
        instanceName = $('#w_t_009 :selected').data('instance');
        $.post('/continuous-assessment2/get-instance',{catagoryId:catagoryId}).done(function(data){
            $('#instanceContainer2').html(data);
        });
        // maxPoint = $('#w_t_009 :selected').data('max');
        // remPoint = $('#w_t_009 :selected').data('rem');
        // $('#maxPointContainer').val(remPoint).parent().show();
        // $('#maxPointContainer').prop({'max':remPoint});

    }

    window.getInstance22 = function(){
        maxPoint = $('#instanceContainer2 :selected').data('maxpoint');
        $('#maxPointContainer').val(maxPoint);
        // console.log(maxPoint);
        $('#maxPointContainer').prop('max',maxPoint);
    }
    window.loadStudent  = function(coffdetID2,courseId2,foredit=null){
        coffdetID = coffdetID2;
        courseId = courseId2;
        loadMarkSheetForm(coffdetID,courseId);
        $('#markSubmissionFormContainer').hide();
        $('#studentListContainer').show();
        $('#enrStdsListContainer').html('Loading students...');
        $.post('/continuous-assessment2/enrstudlists',{instId:coffdetID,courseId:courseId,foredit:foredit},function(data){
            $('#enrStdsListContainer').html(data);
        });
        //console.log(coffdetID, courseId);
    }
    
    window.loadMarkSheetForm = function(coffdetID,courseId){
        $.post('/continuous-assessment2/get-assessment-submission-form',
        {
            instId:coffdetID,
            courseId:courseId
        }).done(function(data){
            resp = JSON.parse(data);
            $('#course-info-contained').html(resp[2]);
            $('#submitmarkContainer').html(resp[1]).parent().show();
        });
    }

    window.markSheet = function(coff2detId){
        myWindow = window.open(
        '/continuous-assessment2/get-mark-sheet-detail?params='+JSON.stringify([coff2detId,23,434]), 
          'Print Students Mark Sheet Report', 
          'height=600,width=900');
        return false;
    }

    window.editMarkSheet = (coffdetId)=>{
        loadStudent(coffdetId,null,1);
    }

    window.gradeReport = function(coff2detId){
        myWindow = window.open(
        '/continuous-assessment2/get-grade-report-detail?params='+JSON.stringify([coff2detId,24,545]), 
          'Print Students Grade Report', 
          'height=900,width=700');
        return false;
    }

    $.post('/course2/courselists',{},function(data){
        //console.log(data);
        $('#courseListContainer').html(data);
        });
    ",
            $this::POS_READY
    );
?>