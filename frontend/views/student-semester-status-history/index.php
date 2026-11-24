<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\StudentSemesterStatusHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */
$this->title = 'Student Semester Status';
$this->params['breadcrumbs'][] = $this->title;
?><br>
<div class="student-semester-status-history-index">
<?php $form = ActiveForm::begin(); ?>
   <div class="container-fluid">
       <div class="row">
           <div class="panel panel-info">
               <div class="panel-heading">
                   <div class="row">
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
                          ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
                                [
                                     'prompt'=>'Select Class Level',

                                      'onChange'=>'
                                        loadGroup(); 

                                      ',
                            ])->label('ClassLevel') ?>
                      </div>


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
                            'onChange'=>'
                                loadGroup();       
                            ',
                        ])->label('Semester') ?>
                       </div>

                       <div class="col-md-2">
                        <?= 
                        $form->field($model, 'group')->dropdownList(
                          ArrayHelper::map(\backend\models\WGroup::find()->all(),'id','group_name'),
                            [
                            'prompt'=>'Select group.',
                            'required'=>TRUE,
                            'onChange'=>'loadStatus()',
                        ])->label('Group') 
                        ?>
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
                       <div class="container-fluid table-responsive" id="studentSemesterStatusContainer" style="border: 1px dashed black;">
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

      acadYearId = $('#studentsemesterstatushistory-sssh_acadyear_relid').val();
      semId = $('#studentsemesterstatushistory-sssh_sem_relid').val();
      admId = $('#studentsemesterstatushistory-admission').val();
      progId = $('#studentsemesterstatushistory-program').val();
      clslvlId = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();
      gsetId = $('#studentsemesterstatushistory-group').val(); 

        params = {
          'admId':admId,
          'progId':progId,
          'acadYearId':acadYearId,
          'semId':semId,
          'clslvlId':clslvlId,
          'gsetId':gsetId,
        };
        
        myWindow = window.open(
          '/student-semester-status-history/get-student-semester-status2-report?params='+JSON.stringify(params), 
          'Print Students Grade Report', 
          'height=700,width=1200');

        myWindow.print();
        // myWindow.close();
        return false;


        // var restorePage = $('body').html();
        // var printContent = $('#studentSemesterStatusContainer').html();
        // $('body').html(printContent);
        // window.print();
        // $('body').html(restorePage);
        // $('.non-print').show();
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
      $.post('/w-group-setup/get-group-lists-peryear',
            {
              acadYearId:$('#studentsemesterstatushistory-sssh_acadyear_relid').val(),
              semId:$('#studentsemesterstatushistory-sssh_sem_relid').val(),
              admId:$('#studentsemesterstatushistory-admission').val(),
              progId:$('#studentsemesterstatushistory-program').val(),
              clslvlId:$('#studentsemesterstatushistory-sssh_clslvl_relid').val(),
              //deptId:$('#studentsemesterstatushistory-department').val(),
              specId:$('#studentsemesterstatushistory-spec').val()
            }, 
            function( data ) {
                $('select#studentsemesterstatushistory-group').html( data ).trigger('change');
            }); 
    }

    window.loadStatus = function(){
      // studentsemesterstatushistory-admission
      $('#studentSemesterStatusContainer').html('Loading Students\' record....');

      acadYearId = $('#studentsemesterstatushistory-sssh_acadyear_relid').val();
      semId = $('#studentsemesterstatushistory-sssh_sem_relid').val();
      admId = $('#studentsemesterstatushistory-admission').val();
      progId = $('#studentsemesterstatushistory-program').val();
      clslvlId = $('#studentsemesterstatushistory-sssh_clslvl_relid').val();
      gsetId = $('#studentsemesterstatushistory-group').val(); 
      // requiredStatus = $('#studentsemesterstatushistory-status').val();


      $.post('/student-semester-status-history/get-student-semester-status2',
      {
        acadYearId:acadYearId,
        semId:semId,
        admId:admId,
        progId:progId,
        clslvlId:clslvlId,
        gsetId:gsetId,

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
