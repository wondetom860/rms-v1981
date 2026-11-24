<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/* @var $model backend\models\User */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['id'=>'accountStatusForm']); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Check status of your account activation | Login to your account
                    </div>
                    <div class="panel-body" style="font-size: 10pt;">
                        <div class="row">
                            <div class="col-md-12 bg-info">
                                You can check your account status by entering your user name or OTP-code.
                            </div>
                        </div>
                        <div class="row" style="background-color: #909FFF">
                            <div class="col-md-12">
                                <?= $form->field($model, 'search1')->textInput(['maxlength' => true])->label('Enter OTP') ?>
                            </div>
                            <div class="col-md-12">
                                <?= $form->field($model, 'search2')->textInput(['maxlength' => true])->label('Enter User Name') ?>
                            </div>
                        </div><hr>
                        <div class="row">
                            <div class="col-md-4"><br>
                                <div class="form-group">
                                    <button type="submit" id="btn_check" onclick="loadAccountStatus();return false;" style="background-color: #88AFCA">Check Status
                                    </button>
                                </div>
                            </div>
                            
                            <div class="col-md-4"><br>
                                <div class="form-group">
                                    <button type="submit" title="Sign in your account" class="pull-right" onclick="loadLoginModal();return false;">Login</button>
                                </div>
                            </div>
                        </div>
                        <div class="row" style="border-top: 1px solid black;">
                            <div class="col-md-12" style=" font-style: italic; font-size: 9pt;" id="accountStatus">
                                <?php if(Yii::$app->session->hasFlash('message')){ ?>
                                    <?= Yii::$app->session->getFlash('message'); ?>
                                <?php }?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php
 $this->registerJs(
    "

    window.loadLoginModal = function(){
        $('#modalGeneralHeader').parent().css('background-color','#8D0');
        $('#modalGeneral').modal('show');
        $('#btn_check').hide();
        $('#modal').find('.modal-header').html('Signin to your account');
        $('#modalContent').load('/user/login');
        $('#modalContent').css('text-align','left');
        $('#modal').find('.modal-dialog').removeClass('modal-lg').addClass('modal-md');
    }
    window.loadAccountStatus = function(){
        form = $('#accountStatusForm');
        $.ajax({
            type:'POST',
            url:'/user/get-account-status',
            data: form.serialize(),
            success:function(data){
                $('#accountStatus').html(data);
            },
            error:function(data){
                $('#accountStatus').html(data);
            },
        });
    }
    ",
    $this::POS_READY);

?>
