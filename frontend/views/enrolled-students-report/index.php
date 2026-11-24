<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\StudentSemesterStatusHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Student Semester Registration Report';
$this->params['breadcrumbs'][] = $this->title;
?><br>
<div class="student-semester-status-history-index">
<?php $form = ActiveForm::begin(); ?>
   <div class="container-fluid">
       <div class="row">
           <div class="panel panel-info">
               <div class="panel-heading" style="text-align: center; align-content: center; align-items: center;">
                   <div class="row">
                    <div class="col-md-2">

                       <?= $form->field($model, 'sssh_acadYear_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\SchoolYear::find()->all(),'id','sch_year_desc'),
                            [
                            'prompt'=>'Select academic year.',
                            'onChange'=>'
                              loadGroup();
                            ',

                            
                        ])->label('Academic Year') ?>
                       </div>

                       <div class="col-md-2">
                        <?= $form->field($model, 'sssh_sem_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\Semester::find()->all(),'id','semester_name'),
                            [
                            'prompt'=>'Select semester.',
                            'onChange'=>'loadGroup();',
                        ])->label('Semester') ?>
                       </div>
                     <div class="col-md-2">

                      <?= $form->field($model, 'admission')->dropdownList(
                          ArrayHelper::map(\backend\models\Admission::find()->all(),'id','admission_name'),
                          [
                          'prompt'=>'Select admission.',
                          'onChange'=>'loadGroup();',
                      ])->label('Admission') ?>
                      
                     </div>
                       <div class="col-md-2">
                        <?= $form->field($model, 'program')->dropdownList(
                            ArrayHelper::map(\backend\models\Program::find()->all(),'id','program_name'),
                            [
                            'prompt'=>'Select Program',
                                'onChange'=>' 
                                    $.post( "/program/clslvllists",
                                        {
                                            progId:$(this).val()
                                        }, 
                                      function( data ) {
                                          //console.log("Returned: "+data);
                                          $("#studentsemesterstatushistory-sssh_clslvl_relid" ).html( data );
                                          loadGroup();       
                                        }); ',


                        ])->label('Program') ?>

                       </div>
              
                      <div class="col-md-2">
                         <?= $form->field($model, 'sssh_clslvl_relId')->dropdownList(
                          [],
                                [
                                     'prompt'=>'Select Class Level',

                                      'onChange'=>'
                                        loadGroup();
                                      ',
                            ])->label('ClassLevel') ?>
                      </div>

                      
                       <div class="col-md-2">
                          <?= $form->field($model, 'dept')->dropdownList(
                              ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_name'),
                              [
                              'prompt'=>'Select semester.',
                              'onChange'=>'
                                  $.post("/specialization/listspec-cp",
                                  {
                                    acadYearId:$("#studentsemesterstatushistory-sssh_acadyear_relid").val(),
                                    semId:$("#studentsemesterstatushistory-sssh_sem_relid").val(),
                                    admId:$("#studentsemesterstatushistory-admission").val(),
                                    progId:$("#studentsemesterstatushistory-program").val(),
                                    clslvlId:$("#studentsemesterstatushistory-sssh_clslvl_relid").val(),
                                    deptId:$("#studentsemesterstatushistory-dept").val(),
                                    specId:$("#studentsemesterstatushistory-spec").val()
                                  }, 
                                  function( data ) {
                                      //console.log("Returned: "+data);
                                      $("#studentsemesterstatushistory-spec" ).html( data );
                                      loadGroup();
                                  }); 
                              ',
                          ])->label('Department') ?>
                       </div>
                       <div class="col-md-2">
                          <?= $form->field($model, 'spec')->dropdownList(
                              [],
                              [
                              'prompt'=>'Select semester.',
                              'onChange'=>'
                                  loadGroup();
                              ',
                          ])->label('Specialization') ?>
                         </div>

                         <div class="col-md-2">
                          <?= $form->field($model, '_gender')->dropdownList(
                              [
                                ''=>' - ',
                                'FEMALE'=>'FEMALE',
                                'MALE'=>'MALE',
                              ],
                              [
                              'prompt'=>'Select Gender.',
                              'onChange'=>'
                                  loadGroup();
                              ',
                          ])->label('Gender') ?>
                         </div>
                         <div class="col-md-2">
                          <?= $form->field($model, 'nat')->dropdownList(
                              ArrayHelper::map(\backend\models\Nationality::find()->all(),'id','name'),
                              [
                              'prompt'=>'Select Nationality.',
                              'onChange'=>'
                                  loadGroup();
                              ',
                          ])->label('Nationality') ?>
                         </div>

                         <div class="col-md-2">
                          <?= $form->field($model, 'spon')->dropdownList(
                              ArrayHelper::map(\backend\models\ScholarshipType::find()->all(),'id','schType_name'),
                              [
                              'prompt'=>'Filter Spnosorship.',
                              'onChange'=>'
                                  loadGroup();
                              ',
                          ])->label('Spnosorship') ?>
                         </div>

                         <div class="col-md-2">
                         <?= $form->field($model, 'userType')->dropdownList(
                          [
                            'MILITARY'=>'MILITARY',
                            'CIVIL'=>'CIVIL'
                          ],
                                [
                                     'prompt'=>'Select User Type',

                                      'onChange'=>'
                                        loadGroup();
                                      ',
                            ])->label('User Type') ?>
                      </div>


                   </div>
                   <div class="row" >
                       <div class="col-md-9">                  
                        <button class="btn btn-primary btn-sm" id="loadStudents">Load Students</button>
                       </div>
                   </div>
               </div>
               <?php ActiveForm::end(); ?>
               <div class="panel-body">
                   <div class="col-md-12 col-lg-12 colsm-12">
                      <hr>
                      <i id="printIcon" style="cursor: pointer; display: none; float: right; color: blue;" title="Print" onclick="printCont('selectionFormContainer')">Print<span class="glyphicon glyphicon-print"></span></i>
                       <div class="container-fluid" id="studentSemesterStatusContainer" style="border: 1px dashed black;">
                           Waiting for your selection...
                       </div>
                   </div>
               </div>
           </div>
       </div>
   </div>

</div>

<?php

 Modal::begin([
    'header'=>'<h4 class="bg-success">Student Admission Completed!!</h4>',
    'id'=>'modal_success',
    'size'=>'modal-sm',
    'options'=>[
        'style'=>[
            'text-align'=>'left',
        ],
    ],
 ]);
 echo "<div id='modalContent' class='jumbotron'>Loading user account form...</div>";
 // echo "<div id='modalFooter' class='container-fluid'><button class='btn btn-sm btn-warning' onclick='submitUserInfo(0)'>Cancel Admission</button></div>";
 Modal::end();
 ?>

<!-- studentSemesterStatusContainer -->

<?=
$this->registerJs(
    "
    window.printCont = function(){
      acadYearId = $('#studentsemesterstatushistory-sssh_acadyear_relid').val();
      semId = $('#studentsemesterstatushistory-sssh_sem_relid').val();
      admId = $('#studentsemesterstatushistory-admission').val();
      progId = $('#studentsemesterstatushistory-program').val();
      clslvlId = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();
      deptId = $('#studentsemesterstatushistory-dept').val(); 
      spec = $('#studentsemesterstatushistory-spec').val(); 
      nat = $('#studentsemesterstatushistory-nat').val(); 
      gender = $('#studentsemesterstatushistory-_gender').val(); 
      spon = $('#studentsemesterstatushistory-spon').val(); 
      userType = $('#studentsemesterstatushistory-usertype').val(); 
      // requiredStatus = $('#studentsemesterstatushistory-status').val();
      // gsetId = $('#studentsemesterstatushistory-group').val(); 
        params = {
            'deptId':deptId,
            'clslvlId':clslvlId,
            'admId':admId,
            'progId':progId,
            'acadYearId':acadYearId,
            'spec':spec,
            'semId':semId,
            'nat':nat,
            'gender':gender,
            'spon':spon,
            'utype':userType,
        };
    myWindow = window.open(
              '/enrolled-students-report/print-student-list?params='+JSON.stringify(params), 
              'Print Students Group Assignment Report', 
              'height=600,width=830');

        // myWindow.print();
        // myWindow.close();
        return false;
    }

    var checkedStds = {};

    $('#gsdgafdghafd').click(function(e){
      e.preventDefault();
      })

    window.generateReport = function(fileName,semregId){
      if(confirm('Generate Semester registration report for this student?')){

        // console.log(fileName,semregId);

        $.post('/enrolled-students-report/generate-report-pdf',
        {
          string1:fileName,
          semregId:semregId
        }).done(function(data){
          // console.log(data);
          // 
          if(data == 1){
            loadGroup();
          }
        });
      }
    }

    window.showReport = function(semregId){ 
      window.open('/enrolled-students-report/get-generated-report?semregId='+semregId,'Print Students Group Assignment Report', 'width=830,height=600');
      return false;
    }
    window.saveOperation = function(){
      //$('#gsdgafdghafd').preventDefault();
      if(confirm('Ready to save the selelectd students\'s semester record?')){
        checkedStdsString = JSON.stringify(checkedStds);
        console.log(checkedStdsString);
        $.post('/student-semester-status-history/save-record',{checkedStdsString:checkedStdsString}).done(function(data){
          if(data == 1){
            alert('Save successfull');
            return false;
          }else{
            alert(data);
          }
          return false;
          });
      }
      return false;
    }

    window.approvedGrade = function(stdId,statusId,clslvlId){
      //console.log(typeof statusId);
      if(typeof statusId == 'undefined' || statusId == ''){
        return false;
      }else{
        // if(jQuery.inArray(stdId,checkedStds) !== -1){ // if item is already selected, remove it because user deselected it.
        //     checkedStds.splice($.inArray(stdId,checkedStds),1);
        // }else{//going to add the item to the list.
        //     //checkedStds.push(stdId);
        //     checkedStds[stdId] = statusId;
        // }
        if(typeof checkedStds[stdId] == 'undefined' || checkedStds[stdId] == null){
          // checkedStds[stdId][0] = statusId;
          // checkedStds[stdId][1] = statusId;
          checkedStds[stdId] = {
            0:statusId,
            1:clslvlId
          };
          console.log(checkedStds);
          return true;
        }else{
          checkedStds[stdId] = null;
          console.log(checkedStds);
          return false;
        }
      }
    }


    window.loadGroup = function(){
      // studentsemesterstatushistory-admission
      $('#studentSemesterStatusContainer').html('Loading Students\' record....');

      acadYearId = $('#studentsemesterstatushistory-sssh_acadyear_relid').val();
      semId = $('#studentsemesterstatushistory-sssh_sem_relid').val();
      admId = $('#studentsemesterstatushistory-admission').val();
      progId = $('#studentsemesterstatushistory-program').val();
      clslvlId = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();
      // gsetId = $('#studentsemesterstatushistory-group').val(); 
      deptId = $('#studentsemesterstatushistory-dept').val(); 
      spec = $('#studentsemesterstatushistory-spec').val(); 
      nat = $('#studentsemesterstatushistory-nat').val(); 
      gender = $('#studentsemesterstatushistory-_gender').val(); 
      spon = $('#studentsemesterstatushistory-spon').val(); 
      userType = $('#studentsemesterstatushistory-usertype').val(); 
      requiredStatus = $('#studentsemesterstatushistory-status').val();


      $.post('/enrolled-students-report/get-enrolled-student-list',
      {
        acadYearId:acadYearId,
        semId:semId,
        admId:admId,
        progId:progId,
        deptId:deptId,
        clslvlId:clslvlId,
        spec:spec,
        requiredStatus:requiredStatus,
        nat:nat,
        gender:gender,
        spon:spon,
        utype:userType

        }).done(function(data){
          $('#studentSemesterStatusContainer').html(data);
          $('#printIcon').show();
        });
    }


    $('#loadStudents').click(function(e){
      e.preventDefault();
      loadGroup();      
      })
    ",
    $this::POS_READY
);
?>