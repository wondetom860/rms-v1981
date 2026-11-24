<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;


/** @var yii\web\View $this */
/* @var $model backend\models\PaymentRedemption */
/* @var $form yii\widgets\ActiveForm */

$this->registerJs(
    "
    select($('#paymentredemption-std_relid').val());
    ",
    $this::POS_READY);
?>

<div class="payment-redemption-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-4 bg-secondary">
            <div class="col-md-12" style="background-color: #99F; padding: 9pt;">
                <div class="col-md-3 hidden">
                    <?= $form->field($model, 'std_relId')->textInput() ?>
                </div>
                <div class="col-md-8">
                    <input type="text" name="student_search_key" class="form-control" placeholder="RDEG/XXXX/XX" id="student_search_key">
                </div>
                <div class="col-md-4">
                    <button class="btn btn-xs btn-primary btn-block form-control" onclick="getStudent();return false;">Get Student</button>
                </div>
                <hr>
                <div class="col-md-12" id="search_result_container" style="max-height: 300px; overflow-y: auto;">
                    <small>----Select Student first------</small>
                </div>
            </div>

            <div class="col-md-12" id="form-container" style="background-color: #9DF; padding:10px;">
                <div class="col-md-6">
                    <?= $form->field($model, 'acadYear_relId')->dropdownList(ArrayHelper::map(\backend\models\SchoolYear::find()->all(),'id','sch_year_desc'),['prompt' => 'Select Academic Year']) ?>
                </div>

                <div class="col-md-6">
                    <?= $form->field($model, 'sem_relId')->dropdownList(ArrayHelper::map(\backend\models\Semester::find()->all(),'id','semester_name'),['prompt' => 'Select Semester']) ?>
                </div>

                <div class="col-md-12">
                    <?= $form->field($model, 'reason')->textarea(['rows' => 1])->hint('For Withdrawal, For Financial Aid, ...') ?>
                </div>

                <div class="col-md-12">
                    <?= $form->field($model, 'comments')->textarea(['rows' => 2]) ?>
                </div>

                <div class="col-md-12">
                    <div class="col-md-12">
                        <div class="form-group pull-right">
                            <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
                        </div>
                    </div>
                </div> 
            </div>
        </div>
        <div class="col-md-8" id="student-profile-container">
                <small>----Select Student...------</small>
        </div>
        <div class="col-md-12 hidden" id="prev_requests_container">
            <small>----Previous payment exemption requests.------</small>
        </div>
    </div>

    <script type="text/javascript">
        window.getStudent = () => {
            student_search_key = $("#student_search_key").val();

            $.post('/payment-redemption/get-student',{student_search_key:student_search_key}).done((response) => { $("#search_result_container").html(response); });
        }

        window.select = (stdId) => {
            $("#paymentredemption-std_relid").val(stdId);
            
            $.get('/student/view?id='+stdId,{}).done(function(data){
                $('#student-profile-container').html(data);
                // $("#form-container").addClass('col-md-4').removeClass('col-md-8');
                // $("#student-profile-container").addClass('col-md-4').removeClass('hidden');
            });

            loadPrevPaymetrequests(stdId);
        }

        window.loadPrevPaymetrequests = (stdId) => {
            $("#prev_requests_container").removeClass('hidden');
            $.post('/payment-redemption/get-prev-requests',{stdId:stdId}).done((response) => { $("#prev_requests_container").html(response); });
        }


    </script>
    <?php ActiveForm::end(); ?>

</div>
