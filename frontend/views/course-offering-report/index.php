<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\StudentSemesterStatusHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Course Offering Report';
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
                              // $.post("/w-group-setup/get-group-lists-peryear",
                              //   {
                              //     acadYearId:$("#studentsemesterstatushistory-sssh_acadyear_relid").val(),
                              //     semId:$("#studentsemesterstatushistory-sssh_sem_relid").val(),
                              //     admId:$("#studentsemesterstatushistory-admission").val(),
                              //     progId:$("#studentsemesterstatushistory-program").val(),
                              //     clslvlId:$("#studentsemesterstatushistory-sssh_clslvl_relid").val(),
                              //     //deptId:$("#studentsemesterstatushistory-department").val(),
                              //     specId:$("#studentsemesterstatushistory-spec").val()
                              //   }, 
                              //   function( data ) {
                              //       //console.log("Returned: "+data);
                              //       $("select#studentsemesterstatushistory-group" ).html( data );
                              //   }); 
                            ',

                            
                        ])->label('Academic Year') ?>
                       </div>

                       <div class="col-md-2">
                        <?= $form->field($model, 'sssh_sem_relId')->dropdownList(
                            ArrayHelper::map(\backend\models\Semester::find()->all(),'id','semester_name'),
                            [
                            'prompt'=>'Select semester.',
                            'onChange'=>'loadGroup()',
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
                                          $("#studentsemesterstatushistory-_clslvl" ).html( data );
                                          loadGroup();       
                                        }); ',


                        ])->label('Program') ?>

                       </div>
              
                      <div class="col-md-2">
                         <?= $form->field($model, '_clslvl')->dropdownList(
                          [],
                                [
                                     'prompt'=>'Select Class Level',
                                     'aria-required'=>FALSE,
                                     'aria-invalid'=>FALSE,

                                      'onChange'=>'loadGroup();',
                            ])->label('Class Level') ?>
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
                                    clslvlId:$("#studentsemesterstatushistory-_clslvl").val(),
                                    deptId:$("#studentsemesterstatushistory-dept").val(),
                                    specId:$("#studentsemesterstatushistory-spec").val()
                                  }, 
                                  function( data ) {
                                      //console.log("Returned: "+data);
                                      $("#studentsemesterstatushistory-spec" ).html( data ).trigger("change");;
                                  }); 
                              ',
                          ])->label('Department') ?>
                         </div>
                         <div class="col-md-2">
                          <?= $form->field($model, 'spec')->dropdownList(
                              [],
                              [
                              'prompt'=>'Select semester.',
                              'onChange'=>'loadGroup();',
                          ])->label('Specialization') ?>
                         </div>
                   </div>
                   <div class="row">
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

<!-- studentSemesterStatusContainer -->

<?=
$this->registerJs(
    "
    window.printCont = function(el){
        // console.log(el);

      // Add the print css to the table. with ID = reportTable
      //$('#reportTable').addClass('table table-bordered');
      $('#reportTable'). css({
        'background-color': \"yellowgreen\",
        \"font-weight\": \"bold\",
        'border':'1px solid black'
        });

        $('.non-print').hide();

      $('#reportTable td,th').css({
        \"font-weight\": \"normal\",
        'borderRight':'1px solid black',
        'borderBottom':'1px solid black'
        });
      $('#reportTable th').css({
        \"font-weight\": \"bold\"
        });

      // $('#reportTable tbody tr').css({
      //   'borderBottom':'1px solid black'
      //   });
      $('#studentSemesterStatusContainer').css({
        'borderTop':'2px solid black',
        'textAlign':'center'
        });

        var restorePage = $('body').html();
        var printContent = $('#studentSemesterStatusContainer').html();
        $('body').html(printContent);
        window.print();
        $('body').html(restorePage);
        $('.non-print').show();
    }

    var checkedStds = {};

    $('#gsdgafdghafd').click(function(e){
      e.preventDefault();
      })

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
      clslvlId = $('#studentsemesterstatushistory-_clslvl').val();
      // gsetId = $('#studentsemesterstatushistory-group').val(); 
      deptId = $('#studentsemesterstatushistory-dept').val(); 
      spec = $('#studentsemesterstatushistory-spec').val(); 
      requiredStatus = $('#studentsemesterstatushistory-status').val();


      $.post('/course-offering-report/get-offered-courses-list2',
      {
        acadYearId:acadYearId,
        semId:semId,
        admId:admId,
        progId:progId,
        deptId:deptId,
        clslvlId:clslvlId,
        spec:spec,
        requiredStatus:requiredStatus

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