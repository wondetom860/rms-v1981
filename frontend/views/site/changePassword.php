<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
 
/** @var yii\web\View $this */
/* @var $model common\models\ChangePasswordForm */
/* @var $form ActiveForm */
 
$this->title = 'Change Password';
?>
<div class="user-changePassword">
	<div class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-body">
 
                <?php $form = ActiveForm::begin(); ?> 
             
                    <?= $form->field($model, '_oldpassword')->passwordInput()->label('Old Password') ?>
                    <?= $form->field($model, 'password')->passwordInput()->label('New Password') ?>
                    <?= $form->field($model, 'confirm_password')->passwordInput()->label('Confirm New Password') ?>
             
                    <div class="form-group">
                        <?= Html::submitButton('Change', ['class' => 'btn btn-primary']) ?>
                    </div>
                <?php ActiveForm::end(); ?>
 
            </div>
        </div>
    </div>
</div>