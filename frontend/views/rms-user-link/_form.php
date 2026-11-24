<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\RmsUserLink */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="rms-user-link-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-6">
            <center><h4>User Information on RMS</h4></center>
            <div class="container-fluid hidden">
                <?= $form->field($model, 'rms_user_id')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'person_relId')->textInput() ?>

                <?= $form->field($model, 'rms_inst_id')->textInput(['maxlength' => true]) ?>

                <?= $form->field($model, 'rms_std_id')->textInput(['maxlength' => true]) ?>
            </div>
            <div class="container-fluid" id="user_information_container_rms">
                <?= $this->render('__report/rms-user-detail',['model' => $model]) ?>
            </div>
        </div>
        <div class="col-md-6">
            <center><h4>User Information on HR-System</h4></center>
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-5">
                        <label for="id_no">ID No. </label>
                        <input type="text" id="id_no" placeholder="Id No" class="form-control">
                        <small>Use Either Military ID/Institution ID</small>
                    </div><div class="col-md-5">
                        <label for="id_no">User Name</label>
                        <input type="text" id="user_name" placeholder="User Name" class="form-control">
                    </div><div class="col-md-2">
                        <br>
                        <button class="btn btn-xs btn-primary pul-right" style="margin-top: 7px;" onclick="getPerson();return false;">Get Person</button>
                    </div>
                </div><hr>
                <div class="col-md-12" id="hrms_person_detail_container">
                    
                </div>
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>     
    </div>

    <script type="text/javascript">

        window.selectPerson = (personId) => {
            $("#rmsuserlink-person_relid").val(personId);
        }

        window.getUserDetail = () => {
            // get detail of a user from rms >>
            $.post('/rms-user-link/get-rms-user-detail',{IdNo:$("#rmsuserlink-rms_user_id").val()}).done((resp) => { $("#user_information_container_rms").html(resp); });
        }
        window.getPerson = () => {
            IdNo = $("#id_no").val();
            user_name = $("#user_name").val();

            $.post('/rms-user-link/get-hrms-user',{IdNo:IdNo,user_name:user_name}).done((resp) => { $("#hrms_person_detail_container").html(resp); });
        }
    </script>

    <?php ActiveForm::end(); ?>

</div>
