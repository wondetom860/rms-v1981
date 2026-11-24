<?php
/** @var yii\web\View $this */
// $this->title='Semester Courses Assigned.'
$this->title = Yii::t('decirs', 'Continuous Assessments');
$this->params['breadcrumbs'][] = $this->title;
?>
<br>
<div class="container-fluid">
    <div class="row">
        <div class="col-md-5">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Your Courses for this semester.
                </div>
                <div class="panel-body">
                    <div class="container-fluid" id="courseListContainer">
                        Loading....
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-7" >
            <div class="container-fluid" style="display: none; background-color: #ed4; padding: 5px;">
                <div class="col-md-4"  id="submitmarkContainer">
                    
                </div>
                <div class="col-md-4">
                    <input type="text" class="form-control" id="instanceContainer" placeholder=" Assessment Instance">
                </div>
                <div class="col-md-3">
                    <input type="number" step="0.01" class="form-control" min="1" id="maxPointContainer" value="0" placeholder="Max possible marks">
                </div>
                
                <div class="col-md-1">
                    <button class="btn btn-xs btn-primary" title="Load Mark Sheet Form" onclick="loadStudent2()" style="margin-top: 10px;">>></button>
                </div>
            </div><br>
            <div class="container-fluid" id="markSubmissionFormContainer" style="border: 1px dashed gold; padding: 5px; color: black; display: none;">
                
            </div><br>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" id="studentListContainer" style="display: none;">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Submited Mark for continous assessment report.
                </div>
                <div class="panel-body">
                    <div class="container-fluid" id="enrStdsListContainer">
                        Loading....
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

<!-- submitmarkContainer -->


<?=
$this->registerJs(
    "
    window.dhfgfvbsfgshgfjsdfatydasdv = function(){
        studentGrades = {};
        succ = 1;
        $.each($('.__markInput'),function(){
            id = $(this).data('stdid');
            mark = Number($(this).val());
            max = Number($(this).attr('max'));
            // console.log(mark,max);

            if((max >= mark) && (mark > 0)){
                studentGrades[id] = mark;
                succ = 1;
            }else{
                succ = 0;
                //$(this).trigger_error('Error');
                $(this).focus().prop('title','Input invalid, Mark must be less than the maximum.' + $(this).attr('max'));
                return false;
            }
        });

        if(succ == 1){
            studentGradesString = JSON.stringify(studentGrades);

           console.log($('#w_t_009 :selected').val());

            $.post(
                '/continuous-assessment/create2',{
                    coffdetId:coffdetID,
                    courseId:courseId,
                    maxPoint:$('#maxPointContainer').val(),
                    instanceName:$('#instanceContainer').val(),
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

        }else{
            alert('Please check the mark, it is not complete');
        }
    }

    window.loadStudent2 = function(){
        if($('#w_t_009 :selected').val() == '' || $('#maxPointContainer').val() == 0 || $('#maxPointContainer').val() < 1){
            alert('Please select assessment category and/or possible max point of the assessment.');
            $('#maxPointContainer').focus();
        }else{
            maxPoint = $('#maxPointContainer').val();
            remPoint = $('#w_t_009 :selected').data('rem');
            if(maxPoint > remPoint){
                alert('You assessment max point is more than remaining point. Please select max point less then '+remPoint);
                $('#maxPointContainer').val(remPoint-1).focus();
            }else{
                 $.post('/continuous-assessment/enrstudlists2',
                    {instId:coffdetID,courseId:courseId, maxPoint:maxPoint},
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
    window.getInstance = function(){
        $('#markSubmissionFormContainer').hide();
        instanceName = $('#w_t_009 :selected').data('instance');
        maxPoint = $('#w_t_009 :selected').data('max');
        remPoint = $('#w_t_009 :selected').data('rem');
        $('#maxPointContainer').val(remPoint).parent().show();
        $('#maxPointContainer').prop({'max':remPoint});
        $('#instanceContainer').val(''+$('#w_t_009 :selected').data('instance'));

    }
    window.loadStudent  = function(coffdetID2,courseId2){
        coffdetID = coffdetID2;
        courseId = courseId2;
        $('#markSubmissionFormContainer').hide();
        $('#studentListContainer').show();
        $('#enrStdsListContainer').html('Loading students...');
        $.post('/continuous-assessment/enrstudlists',{instId:coffdetID,courseId:courseId},function(data){
            $('#enrStdsListContainer').html(data);
            loadMarkSheetForm(coffdetID,courseId);
            // loadStudent2();
            getInstance();
            });
        //console.log(coffdetID, courseId);
    }
    
    window.loadMarkSheetForm = function(coffdetID,courseId){
        $.post('/continuous-assessment/get-assessment-submission-form',
        {
            instId:coffdetID,
            courseId:courseId
        }).done(function(data){
            $('#submitmarkContainer').html(data).parent().show();
        });
    }

    $.post('/course2/courselists',{},function(data){
        //console.log(data);
        $('#courseListContainer').html(data);
        });
    ",
    $this::POS_READY
);
?>