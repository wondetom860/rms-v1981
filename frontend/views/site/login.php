<?php
use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

/** @var yii\web\View $this */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model \common\models\LoginForm */

// $this->title = 'rms - '.Yii::t('rms', 'Sign In');


$fieldOptions1 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-user form-control-feedback'></span>"
];

$fieldOptions2 = [
    'options' => ['class' => 'form-group has-feedback'],
    'inputTemplate' => "{input}<span class='glyphicon glyphicon-lock form-control-feedback'></span>"
];
?>
<div class="container-fluid">
  <div class="row">
    <div class="col-md-12">
    <div class="login-box">
        <!-- /.login-logo -->
        <div class="login-box-body">
            <p class="login-box-msg"><?php echo Yii::t('rms', 'Sign in to start your session');?></p>
            
            <?php $form = ActiveForm::begin(['id' => 'login-form', 'enableClientValidation' => false]); ?>

            <?= $form
                ->field($model, 'username', $fieldOptions1)
                ->label(false)
                ->textInput(['placeholder' => Yii::t('rms', $model->getAttributeLabel('username'))]) ?>

            <?= $form
                ->field($model, 'password', $fieldOptions2)
                ->label(false)
                ->passwordInput(['placeholder' => Yii::t('rms',$model->getAttributeLabel('password'))]) ?>

            <div class="row">           
                <div class="col-xs-4">
                    <?= Html::submitButton(Yii::t('rms', 'Sign In'), ['class' => 'btn btn-primary btn-block btn-flat', 'name' => 'login-button']) ?>
                </div>
                 
            </div>


            <?php ActiveForm::end(); ?>

        </div>
        <!-- /.login-box-body -->
    </div><!-- /.login-box -->
</div>
</div>
</div>
