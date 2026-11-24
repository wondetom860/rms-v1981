<?php

use yii\helpers\Html;
use yii\bootstrap\ActiveForm;
use yii\helpers\ArrayHelper;
use yii\web\View;
use common\models\User;
use yii\db\ActiveQuery;
use kartik\date\DatePicker;

$this->title = Yii::t('rms', 'New Student Admission');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="site-signup">
    <h1><?= Html::encode($this->title) ?></h1>


    <div class="row">
        <div class="col-lg-4">
            <div class="panel panel-default">
                <div class="panel-heading">
                    <?= Yii::t('rms','Basic Info') ?> #1
                </div>
                <div class="panel-body">
                    <?php $form = ActiveForm::begin(['id' => 'form-signup']); ?>


                    <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'last_name')->textInput() ?>

                    <?= $form->field($model, 'gender')->dropDownList([ 'MALE' => 'MALE', 'FEMALE' => 'FEMALE', ], ['prompt' => ' ','value'=>'MALE']) ?>

                    <?= $form->field($model, 'user_type')->textInput(['maxlength' => true, 'value'=>'Military']) ?>

                    <?= $form->field($model, 'nationality')->textInput(['maxlength' => true]) ?>


                </div>
            </div>

        </div>
        <div class="col-md-4">
            <div class="panel panel-primary">
                <div class="panel-heading">Basic Student info #1</div>
                <div class="panel-body">
                    
                    <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'dob')->widget(DatePicker::className(),[
                            'pluginOptions'=>[
                                'autoClose'=>true,
                                'format'=>'yyyy-m-dd',
                                'min'=>date('yyyy-m-d'),
                            ],
                        ])
                     ?>
                     <?= $form->field($model, 'username')->textInput() ?>

                    <?= $form->field($model, 'password')->passwordInput() ?>

                </div>
            </div>
        </div>
        <div class="col-md-4">
            <div class="form-group">
                <?= Html::submitButton(Yii::t('dmshub','Signup'), ['class' => 'btn btn-primary', 'name' => 'signup-button']) ?>
            </div>
        </div>
        <?php ActiveForm::end(); ?>
    </div>
</div>
<?php
$script = <<<EOD
    
$("#memriya").hide();
$("#air_force").hide();
$("#br").hide();
$("#signupform-kifl_id").change(function(){
   var va = $("#signupform-kifl_id").val();
        if(va == '2'){
        $("#memriya").show();
        $("#air_force").hide();
        $("#br").hide();
        }
        else if(va == '3'){
        $("#air_force").show();
        $("#memriya").hide();
        $("#br").hide();
         }
        else if (va == '1'){
           $("#br").show();
           $("#memriya").hide();
           $("#air_force").hide();
           }
});


EOD;
$this->registerJs($script, View::POS_END);
?>

