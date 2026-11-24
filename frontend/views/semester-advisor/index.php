<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use yii\bootstrap\Modal;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\PaymentReasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decirs', 'Semester Advisors and/or Semester PG Co-ordinators');
$this->params['breadcrumbs'][] = $this->title;
?><br>
<div class="semester-completion-check-index">
    <div class="container-fluid">
        <div class="row" style="font-size: 10pt;">
            <div class="panel panel-info">
                <div class="panel-heading">
                    <h4 style="text-align: center; text-decoration: underline; font-stretch: semi-condensed;">Semester Advisor(s) and / or PG Co-ordinator(s)</h4> 
                    <?php $form = ActiveForm::begin(); ?>
                    <div class="row" style="background-color: #8D8;">
                        <div class="col-md-1">

                            <?=
                            $form->field($model, 'admission')->dropdownList(
                                    ArrayHelper::map(\backend\models\Admission::find()->all(), 'id', 'admission_name'), [
                                'prompt' => 'Select admission.',
                                'required' => TRUE,
                            ])->label('Admission')
                            ?>

                        </div>
                        <div class="col-md-2">
                            <?=
                            $form->field($model, 'program')->dropdownList(
                                    ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'), [
                                'prompt' => 'Select Program',
                                'required' => TRUE,
                                'onChange' => ' 
                                            $.post( "/program/clslvllists",
                                                {
                                                    progId:$(this).val()
                                                }, 
                                              function( data ) {
                                                 console.log("Returned: "+data);
                                                  $("#studentsemesterstatushistory-_clslvl" ).html( data ).trigger("change");
                                                  //loadGroup();       
                                            }); ',
                            ])->label('Program')
                            ?>

                        </div>

                        <div class="col-md-2">
                            <?php
                            $dept = [];
                            if(Yii::$app->user->can('DepartmentHead')){
                                $instDept = \backend\models\Instructor::find()->where(['inst_user_relId'=>Yii::$app->user->identity->id])->one()->inst_dep_relId;
                                $dept = \backend\models\Department::find()->where(['id'=>$instDept])->all();
                            }else{$dept = \backend\models\Department::find()->all();}
                            ?>
                            <?=
                            $form->field($model, 'department')->dropdownList(
                                    ArrayHelper::map($dept, 'id', 'dept_name'), [
                                'prompt' => 'Select Department',
                                    // 'onChange'=>' 
                                    //     $.post( "/program/clslvllists",
                                    //         {
                                    //             progId:$(this).val()
                                    //         }, 
                                    //       function( data ) {
                                    //          console.log("Returned: "+data);
                                    //           $("#studentsemesterstatushistory-sssh_clslvl_relid" ).html( data ).trigger("change");
                                    //           //loadGroup();       
                                    //     }); ',
                            ])->label('Department')
                            ?>

                        </div>

                        <div class="col-md-1">
                            <?=
                            $form->field($model, '_clslvl')->dropdownList(
                                    ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'), [
                                'prompt' => 'Select Class Level',
                                'onChange' => '',
                            ])->label('ClassLevel')
                            ?>
                        </div>
                        <div class="col-md-2">
                            <?=
                            $form->field($model, 'acadYearId2')->dropdownList(
                                    ArrayHelper::map(\backend\models\SchoolYear::find()->all(), 'id', 'sch_year_desc'), [
                                'prompt' => 'Select Academic Year',
                            ])->label('Academic Year')
                            ?>
                        </div>
                        <div class="col-md-2">
                            <?=
                            $form->field($model, 'semId2')->dropdownList(
                                    ArrayHelper::map(\backend\models\Semester::find()->all(), 'id', 'semester_name'), [
                                'prompt' => 'Select Semester',
                            ])->label('Semester')
                            ?>
                        </div>
                        <div class="col-md-2"><br>
                            <button class="btn btn-primary btn-md" style="margin-top: 5px;" id="loadStudents">Show</button>
                        </div>
                    </div>
                    <?php ActiveForm::end(); ?>
                </div>
                <div class="panel-body">
                    <!-- <div class="col-md-4 col-lg-4 col-sm-4">
                        <div class="container-fluid" id="StudentListContainer" style="background-color: #dd9">
                            Waiting...
                        </div>
                    </div> -->
                    <div class="col-md-12 col-lg-12 colsm-12">
                        <div class="container-fluid table-responsive" id="formContainer" style="border: 1px solid black; display: none; margin: 5px;">
                            Waiting for your selection...
                        </div>
                        <!-- <i id="printIcon" style="cursor: pointer; display: none; float: right; color: blue;" title="Print" onclick="printCont()">Print<span class="glyphicon glyphicon-print"></span></i> -->
                        <div class="container-fluid" id="studentSemesterGradeReportContainer" style="border: 0px dashed black;">
                            Waiting for your selection...
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>

<?php
$this->registerJs(
        "

        window.changeSA = function(id){
            if(confirm('Change Advisor?')){
                $.post('/semester-advisor/delete',{id:id}).done(function(data){
                    if(data == 1){
                        loadStudentsInGroup();loadForm();
                    }else{
                        alert(data);
                    }
                });
            }
        }

        window.assignAdvisor  = function(admId,progId,deptId,clslvlId,instId){
            if(confirm('Assign as Advisor?')){
                console.log(admId,progId,deptId,clslvlId,instId);
                console.log('Admission: '+admId);
                $.post('/semester-advisor/create',{
                    admId:admId,
                    progId:progId,
                    deptId:deptId,
                    clslvlId:clslvlId,
                    instId:instId,
                }).done(function(data){
                    if(data == 1){
                        loadStudentsInGroup();loadForm();
                    }else{
                        $('#studentSemesterGradeReportContainer').html(data);
                    }
                });
            }
        }
        window.loadForm = function(){
            $.post('/semester-advisor/form').done(function(data){
                $('#formContainer').html(data).show();
            });
        }

        window.printCont = function(el){
            // console.log(el);
            $('.non-print').hide();
            var restorePage = $('body').html();
            var printContent = $('#studentSemesterGradeReportContainer').html();
            $('body').html(printContent);
            window.print();
            $('body').html(restorePage);
        }


        window.loadStudentsInGroup = function(){
            $('#printIcon').hide();
            $('#studentSemesterGradeReportContainer').html('Loading semester advisors');
            // $('#StudentListContainer').html('Loading Instructors for your selection.');

            // Load students assigned to the group in the selected academic year adn semester.

                  admId = $('#studentsemesterstatushistory-admission').val();
                  progId = $('#studentsemesterstatushistory-program').val();
                  clslvlId = $('#studentsemesterstatushistory-_clslvl').val();
                  deptId = $('#studentsemesterstatushistory-department').val(); 


                  acadYearId = $('#studentsemesterstatushistory-acadyearid2').val(); 
                  semId = $('#studentsemesterstatushistory-semid2').val(); 

             $.post('/semester-advisor/report',
                    {
                        
                        admId:admId,
                        progId:progId,
                        clslvlId:clslvlId,
                        deptId:deptId,
                        acadYearId:acadYearId,
                        semId:semId

                    }
                    ).done(function(data){
                $('#studentSemesterGradeReportContainer').html(data);
                $('#printIcon').show();
             });



        }
        $('#loadStudents').click(function(e){
            e.preventDefault();
            loadStudentsInGroup();
        });
        ", $this::POS_READY);
?>
