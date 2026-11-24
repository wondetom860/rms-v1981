<?php
use yii\helpers\Html;
use yii\widgets\ActiveForm;
 
/** @var yii\web\View $this */
/* @var $model common\models\Change User Name */
/* @var $form ActiveForm */
 
$this->title = 'Change User Name';
?>
<div class="user-changePassword">
	<div class="col-md-5">
        <div class="panel panel-default">
            <div class="panel-heading">
                Change User Name
            </div>
            <div class="panel-body">
 
                <?php $form = ActiveForm::begin(); ?> 
           
                    <?= $form->field($model, 'oldUserName')->textInput()->label('Old User Name') ?>
                    <?= $form->field($model, 'newUserName')->textInput()->label('New User Name') ?>
                    <?= $form->field($model, 'confirmNewUserName')->textInput()->label('Confirm New User Name') ?>
             
                    <div class="form-group">
                        <?= Html::submitButton('Change', ['class' => 'btn btn-primary']) ?>
                    </div>
                <?php ActiveForm::end(); ?>
 
            </div>
        </div>
    </div>
</div>