<?php

// use yii;
$this->title = "Semester Evaluation";
$this->params['breadcrumbs'][] = $this->title;

?>

<div class="conatiner-fluid">
    <div class="row">
        <div class="col-md-8">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    New semester Evaluation form
                </div>
                <div class="panel-body">
                    <div class="conatiner-fluid" style="background-color: #CDF; padding: 5px;">
                        Select Evaluation Type and an evaluee ( A person to be evaluated.)<hr>
                        <div class="row">
                            <div class="col-md-4" id="EvaluationTypeSelectionContainer">
                                <div id="EvaluationTypeSelection">
                                    
                                </div>
                            </div>
                            <div class="col-md-8">
                                <div class="row" id="EvalueeSelectionContainer">
                                    <div class="col-md-6" id="EvalueeSelection">
                                    
                                    </div>
                                    <div class="col-md-6" id="EvalueeCourseSelection">
                                        
                                    </div>
                                </div>
                                
                            </div>
                        </div>
                    </div><br>
                    <div class="conatiner-fluid" id="evalPointsContainer" style="background-color: #DDE; padding: 5px;">
                    </div>
                </div>
                <div class="panel-footer">
                    <button class="btn btn-success btn-md" onclick="saveEvaluation()">
                        Submit
                    </button>
                </div>
            </div>
        </div>

        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading" id="sfghsgfgsjd">
                    Evaluation Results 
                </div>
                <div class="panel-body" id="evalStatusContainer" style="display: none; background-color: #333; color: white;">
                    Evaluee: <span id="evalueeName" >not selected</span>
                    <p>Total pt: <span id="totalgvn" > 0 </span>/<span id="totalpt"> 0 </span> = <span id="totalavg"> 0 </span></p>
                    
                </div>
                <div class="panel-footer">
                    
                </div>
            </div>
        </div>
    </div>
</div>

<!-- EvaluationTypeSelectionContainer -->
<!-- EvalueeContainer -->

<!-- evalPointsContainer -->

<!-- EvalueeCourseSelection -->

<?php

$this->registerJs(
    "
    var totalGvn = 0;
    var total = 0;
    var evalTypeSelected = null;
    succ = 1;
    course = '';
    okk = 0;
    averageptGiven = 0;

    window.saveEvaluation = function(){
        evalueeId = $('#22222222222222').val();
        course = $('#ddddd').val();

        console.log(course);

        try{
            if(course == '' || typeof course == 'undefined' || course == ' ' || course == null){
                course = 99;
                //console.log('EvalTypeId: '+evalTypeSelected);
                averageptGiven = $('#eval_type_'+evalTypeSelected).val();
                console.log('AVG PT. gven: '+averageptGiven);
                if(averageptGiven > Number($('#eval_type_'+evalTypeSelected).data('max'))){
                    $('#eval_type_'+evalTypeSelected).focus();
                    $('#eval_type_'+evalTypeSelected).prop('title','Invalid Input');
                    succ = 0;
                }
            }else{
                averageptGiven = totalGvn / total;
            }
            //console.log('Course: '+course);
            // averageptGiven = totalGvn / total;

            //evalType = $('#EvaluationTypeSelection').val();
            $('.ser_semev_point').css({'readOnly':true});
            $.each($('.ser_semev_point'),function(){
                if($(this).val() == ''|| $(this).val() == 'undefined' || $(this).val() == ' '){
                    alert('Evaluation incomplete, please rate all the points.');
                    $(this).parent().focus();
                    succ = 0;
                    return 0;
                }
            });

                console.log(succ);
                    
                if(succ !== 0){
                    console.log('AVG-Point:'+averageptGiven);
                    $.post(
                        '/semester-evaluation/save-evaluation',
                        {
                            evalTypeSelected:evalTypeSelected,
                            semevRecId:evalueeId,
                            averageptGiven:averageptGiven,
                            courseId:course

                            },
                        function(data){
                            //console.log(data);
                            if(data == 1){
                                    window.location = window.location.href;
                                }else{
                                    $('#eval_type_'+evalTypeSelected).focus();
                                    $('#eval_type_'+evalTypeSelected).prop('title','Invalid Input');
                                    $('#sfghsgfgsjd').html(data);   
                                }
                            });
                }

            // console.log(evalueeId, evalTypeSelected, total);
            // console.log('Max Point: '+$('#pointEvalScale').attr('max'));

        }catch(e){
            alert('Evaluation incomplete, please rate all the points.');
            return 0;
        }
    }

    window.addPoint = function(elId){
        hiddenText = Number($('#ser_semev_'+elId).val());
        givenRate = Number($('#ser_semev_point'+elId).val());

        // console.log(givenRate, hiddenText);

        totalGvn -= hiddenText;
        totalGvn += givenRate;

        $('#ser_semev_'+elId).val(givenRate);
        $('#totalgvn').text(totalGvn);

        $('#totalavg').text((totalGvn/total * 100) + '%');
        $('#evalStatusContainer').show();

    }

    window.loadEvalueeCourses = function(){
        //console.log('Evaluee ID: '+$('#22222222222222').val());
        // $('#evalPointsContainer').html('Loading Evaluee Courses');
        $.post('/semester-evaluation/get-evaluee-courses',
        {
            semevRecId:$('#22222222222222').val(),
            evalType: evalTypeSelected

        },
        function(data){
            dd = JSON.parse(data);
            if(dd['ok'] == 1){
                $('#EvalueeCourseSelection').html(dd['evaluee']);
                loadEvalPoints();
            }else if(dd['ok'] == 2){//No need of getting evaluation points/keys
                $('#EvalueeCourseSelection').html(dd['evaluee']);
                //loadEvalPoints();
                //console.log('Calling rateScale');
                //loadRatingScale();
            }
            else{
                $('#EvalueeCourseSelection').html(dd['message']);
            }
            });
    }

    window.loadRatingScale = function(){
        course = $('#ddddd').val();
        if((course == ''|| course == 'undefined' || course == ' ') && (evalTypeSelected != 8 && evalTypeSelected != 6 && evalTypeSelected != 11 && evalTypeSelected != 13)){
            $('#ddddd').focus();
            $('.ser_semev_point').html(' - ');
            //alert('Yes!  '+evalTypeSelected);
            }else{
                $.post('/semester-evaluation/get-rating-scale2',
                {
                    semevRecId:$('#22222222222222').val(),
                    evalType:evalTypeSelected,
                    course:$('#ddddd').val(),
                    },
                function(data){
                    //console.log(data);
                    pdata = JSON.parse(data);
                    if(pdata['ok'] == 1){
                            $('.ser_semev_point').html(pdata['rs']);
                            total = Number($('#totalCount').text()) * 5;
                            //console.log(total);
                            $('#totalpt').text(total);
                        }else if(pdata['ok'] == 2){
                            $('#evalPointsContainer').html(pdata['rsN'])

                        }
                        else{
                            $('#evalPointsContainer').html(pdata['message'] + pdata['progError']);
                        }
                });
            }
    }

    window.loadEvalPoints = function(){
        totalGvn = 0;
        total = 0;
        tt = $('#22222222222222 :selected').html().toLocaleString();
        //console.log('User selected: '+tt);
        $('#evalueeName').text(tt);
        $.post('/semester-evaluation/get-points',{evalType:evalTypeSelected},function(data){
                $('#evalPointsContainer').html(data);
                //loadRatingScale();
                //console.log(evalTypeSelected);
                // ser_semev_point
            });
    }
    window.GetPerson = function(evalType){
        total = 0;
        totalGvn = 0
        evalTypeSelected = evalType;
        //console.log(evalType);
        $.post(
            '/semester-evaluation/get-evaluee-to-evaluate2',
            {
                evalType:evalType
                },
            function(data){
                pdata = JSON.parse(data);
                if(pdata['ok'] == 1){
                    $('#EvalueeSelection').html(pdata['evaluee']);
                    // console.log(pdata['evaluee']);
                }else if(pdata['ok'] == 2){
                    $('#EvalueeSelection').html(pdata['evaluee']);
                }
                else{
                    $('#EvalueeSelection').html(pdata['message']);
                }
                //loadEvalPoints();
            })
    }

     window.loadEvaluationType = function(){
        //$('#EvaluationTypeSelectionContainer').html('Loading evaluations types for you...');
        $.post(
            '/semester-evaluation/get-evaluation-type',
            {},
            function(data){
                if(isJson(data)){
                    pdata = JSON.parse(data);
                    if(pdata['ok'] == 1){
                        $('#EvaluationTypeSelection').html(pdata['evalOpt']);
                        //console.log(pdata['evalOpt']);
                    }else{
                        $('#EvaluationTypeSelectionContainer').html(pdata['message']);
                    }
                }else{
                    $('#EvaluationTypeSelectionContainer').html(data);
                }
            })
     }

     window.isJson = function(str) {
        try {
            JSON.parse(str);
        } catch (e) {
            return false;
        }
        return true;
    }

     window.loadEvaluee = function(evalType){
        GetPerson(0);
     }


     loadEvaluationType();
     //loadEvaluee(0);
    ",
    $this::POS_READY
);


?>
