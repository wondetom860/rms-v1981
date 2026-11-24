<?php
 
/** @var yii\web\View $this */
/* @var $form yii\bootstrap\ActiveForm */
/* @var $model common\models\LoginForm */

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;

$this->title = Yii::$app->name . " | Home Page"; 

//$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-index"> 
    <div class="panel panel-primary">
      <div class="panel panel-heading">
        <div class="row">
           <div class="col-lg-5">
             <h2><?= Html::encode(\Yii::$app->params['client']) ?></h2>
           </div>
           <div class="col-lg-2">
               logo
           </div>
           <div class="col-lg-4">
             <h3><?= Html::encode(\Yii::$app->params['project']) ?></h3>
           </div>
       </div>
      </div>
      <div class="panel panel-body"> 
          
        <div class="site-login">
            <h2><?= Html::encode($this->title) ?></h2> 

            <div class="row">
                <div class="col-lg-5"> 
                    <?php $form = ActiveForm::begin(['id' => 'login-form']); ?>

                        <?= $form->field($model, 'username')->textInput(['autofocus' => true]) ?>

                        <?= $form->field($model, 'password')->passwordInput() ?>

                        <?= $form->field($model, 'rememberMe')->checkbox() ?>

                        <!-- div style="color:#999;margin:1em 0">
                            If you forgot your password you can <?= Html::a('reset it', ['site/request-password-reset']) ?>.
                        </div> -->

                        <div class="form-group">
                            <?= Html::submitButton('Login', ['class' => 'btn btn-primary', 'name' => 'login-button']) ?>
                        </div>

                    <?php ActiveForm::end(); ?>
                  
                </div>
            </div>
       </div>
     
      </div>
    </div>
     
</div>
