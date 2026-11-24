<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
?>

<div class="row" style="padding: 0px; margin: 0px;">
    <div class="col-md-12" id="d_login_2">
        <?php
            // $model = new LoginForm();
            $fieldOptions1 = [
                'options' => ['class' => 'form-group has-feedback'],
                'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
            ];

            $fieldOptions2 = [
                'options' => ['class' => 'form-group has-feedback'],
                'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
            ];
        ?>
        <div class="row" style=" padding: 0px; margin: 0px; font-size: 9pt;">
            <div class="col-md-12" style="background:transparent; padding: 0px; margin: 0px;">
                <?php $form = ActiveForm::begin(['id' => 'login_form','options'=>['onsubmit'=>'formValidate();return false;'], 'enableClientValidation' => true,'validationUrl'=>'/user/validate-user-login']); ?>
                <div class="row">
                    <div class="col-md-12">
                        <?= $form
                            ->field($model, 'username', $fieldOptions1)
                            ->label(false)
                            ->textInput(['placeholder' => Yii::t('rms', $model->getAttributeLabel('username'))]) ?>

                    </div>  
                    <div class="col-md-12">
                        <?= $form
                            ->field($model, 'password', $fieldOptions2)
                            ->label(false)
                            ->passwordInput(['placeholder' => Yii::t('rms',$model->getAttributeLabel('password'))]) ?>
                    </div>           
                    <div class="col-md-6" style="">
                        <?= Html::submitButton(Yii::t('rms', 'Sign In'), ['class' => 'btn btn-sm btn-block btn-flat btn-link', 'name' => 'login-button','id'=>'loginButton']) ?>
                    </div>
                    <div class="col-md-6">
                        <button class="btn btn-block btn-sm btn-flat btn-link" onclick="loadModalWithContent(); return false;" title="Check activation status of your account.">Check Acct.</button>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12" style=" color: red;">
                        <?php if(Yii::$app->session->hasFlash('message')){ ?>
                            <?= Yii::$app->session->getFlash('message'); ?>
                        <?php }?>
                        <span id="loginMessage">...</span>
                    </div>
                </div>
                <?php ActiveForm::end(); ?>
            </div>
        </div>
    </div>
    <!--  -->
</div>

<?php
    $this->registerJs(
        "
        window.loadModalWithContent = ()=>{
            $('#modal').find('.modal-header').html('Check your account status');
            $('#modalContent').css('text-align','left');
            $('#modal').find('.modal-dialog').removeClass('modal-md').addClass('modal-lg');
            $.post('/user/get-account-status',{}).done(function(data){
                $('#modalContent').html(data);
            });
            return false;
        }
        $('#loginButton').click(function(event){
            event.preventDefault();
            $('#loginMessage').html('Attempting login....');
            if(formValidate()){
                $.ajax({
                    type:'post',
                    url:'/user/login',
                    data:$('#login_form').serialize(),
                    success:function(data){
                        try{
                            // d = JSON.parse(data);
                            // console.log(data.loginform-password);
                            if(d['loginform-password'] != undefined){
                                $('#loginMessage').html(d['loginform-password']);
                            }else{
                                $('#loginMessage').html('Error : Password or User name Error. Please correct and retry.');
                            }
                        }catch(e){
                            $('#loginMessage').html('Error : Password or User name Error. Please correct and retry.');
                        }
                        },
                    error:function(data){
                        console.log('ERROR: '+data);
                    }

                });
            }

            return false;
            
        });

        window.formValidate = function(){
            $('#login_form').data('yiiActiveForm').submitting = true;
            return ( typeof $('#login_form').yiiActiveForm('validate') == 'undefined');
        }
        ",
        $this::POS_READY
    );

?>