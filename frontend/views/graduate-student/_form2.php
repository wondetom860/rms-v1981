<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;


/** @var yii\web\View $this */
/* @var $model backend\models\GraduateStudent */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="graduate-student-form">

    <div class="row">
        <div class="col-md-12">
            <?php
                if (Yii::$app->session->hasFlash('message')) {
                    ?>
                    <div class="alert alert-info">
                        <?=  Yii::$app->session->getFlash('message') ?>
                    </div>
                    <?php
                }
            ?>
        </div>
        <div class="col-md-5">
            <div class="container-fluid">
                <button class="btn btn-block btn-xs btn-primary" onclick="getUsersfromDECRMS();return false;">Import from RMS-<?= Yii::$app->params['college_accr'] ?></button><hr>
                <div class="container-fluid" id="usersList" style="height: 440px; overflow-y: auto; background-color: #FDFDFD; padding: 10px;">
                    
                </div>
            </div>
            <!-- <center ><br><br><br>
                <img src="/uploads/<?= $modelUser->photo_num ?>" alt="photo" class="img-circle" alt="Profile Image" height="200" width="180">
                <p><?= $modelUser->first_name .' '. $modelUser->middle_name .' '. $modelUser->last_name ?></p>
            </center> -->
        </div>
        <div class="col-md-7" id="formContaimer">
            <div class="panel panel-default">
                <div class="panel-heading">
                    Graduate Student Information
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(); ?>

                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'fullname')->textInput(['maxlength' => true,'readonly' => true,'required' => true])  ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'gstd_profession_category')->dropdownList([
                                'MILITARY'=>'  Military  ',
                                'CIVIL'=>'  Civil  ',
                                'FOREGN'=>'   Foreign  ',
                            ],['prompt'=>'User Type'])->label('Prof. Category')  ?>
                        </div>

                        <div class="col-md-3">
                            <?= $form->field($model, 'gstd_rank')->dropDownList(ArrayHelper::map(\backend\models\MilitaryRank::find()->all(),'mr_rank_name',function($model){
                                return $model->mr_rank_name.' ('. $model->mrCategoryRel->catagory_name .')';
                            }), 
                                ['prompt' => 'Select Rank']) 
                            ?>
                        </div>
                    </div>
                    <div class="row">

                        <div class="col-md-4">
                            <?= $form->field($model, 'gstd_regId')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'gstd_adm_relId')->dropDownList(ArrayHelper::map(\backend\models\Admission::find()->all(),'id','admission_name'), 
                            ['prompt' => 'Select Admission'])  ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'gstd_prog_relId')->dropDownList(ArrayHelper::map(\backend\models\Program::find()->all(),'id','program_name'), 
                            ['prompt' => 'Select Program'])  ?>
                        </div>
                        
                    </div>
                    <div class="row">
                        <div class="col-md-6">
                            <?= $form->field($model, 'gstd_dept_relId')->dropDownList(ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_name'), 
                            [
                                'prompt' => 'Select Department',
                                'onchange' => '
                                    $.post("/graduate-student/speclists",
                                            {
                                                deptId:$(this).val(),
                                                admId:$("#graduatestudent-gstd_adm_relid").val(),
                                                progId:$("#graduatestudent-gstd_prog_relid").val(),
                                                forgstd:1

                                            }).done(function(data){
                                        $("#graduatestudent-gstd_spec_relid").html(data);
                                    });
                                ',
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                        <?= $form->field($model, 'gstd_spec_relId')->dropDownList(ArrayHelper::map(\backend\models\Specialization::find()->all(),'id','spec_name'), 
                            ['prompt' => 'Select Specialization'])  ?>  
                        </div>
                        
                    </div>    
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'gstd_sponsorship_relId')->dropDownList(ArrayHelper::map(\backend\models\ScholarshipType::find()->all(),'id','schType_name'), 
                            ['prompt' => 'Sponsorship'])  ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'gstd_adm_year')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'gstd_grad_year')->textInput(['maxlength' => true]) ?>
                        </div>
                        
                    </div>      
                    <div class="row">
                        <div class="col-md-3">
                           <?= $form->field($model, 'gstd_prev_CGPA')->textInput() ?> 
                        </div>
                        <div class="col-md-3">
                           <?= $form->field($model, 'gstd_enterance_exam')->textInput() ?> 
                        </div>
                        <div class="col-md-3">
                           <?= $form->field($model, 'gstd_CGPA')->textInput() ?> 
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'gstd_date_of_graduation')->widget(DatePicker::className(),[
                                'pluginOptions'=>[
                                    'autoClose'=>true,
                                    'format'=>'yyyy-m-dd',
                                ],
                            ])?>
                        </div>
                    </div>
                    <div class="row">
                        <h4 style="border-bottom: 1px solid black;">Educational Doucument Related Record</h4>
                        <div class="col-md-4"><br>
                            <?= $form->field($model, 'gstd_doc_collected')->checkBox([
                                'Yes'=>1,'NO'=>0
                            ]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'gstd_doc_collected_at')->widget(DatePicker::className(),[
                                'pluginOptions'=>[
                                    'autoClose'=>true,
                                    'format'=>'yyyy-m-dd',
                                ],
                            ])?>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group pull-right"><br>
                                <?php echo Html::activeHiddenInput($model, "gstd_user_relId");?>
                                <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                            </div>
                        </div>
                    </div> 
                    <?php ActiveForm::end(); ?>
                </div>
            </div>
        </div>
    </div>

</div>

<script type="text/javascript">
    window.getUsersfromDECRMS = ()=>{
        // $("#usersList").css({
        //     'height':$("#formContaimer").height;
        // });
        $('#usersList').html("Loading users from RMS-DEC...");
        $.post('/graduate-student/get-all-users',{}).done((data)=>{
            $('#usersList').html(data);
        });
    }
</script>