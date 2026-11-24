<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/* @var $model backend\models\User */
/* @var $form yii\widgets\ActiveForm */
$model = & $sessionForm['models']['modelUser'];
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(['id'=>'modelUser']); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-default">
                    <div class="panel-heading">
                        Basic user information
                    </div>
                    <div class="panel-body">
                        <div class="row">
                            <div class="col-md-3">
                                <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'gender')->dropDownList([ 'MALE' => 'MALE', 'FEMALE' => 'FEMALE', ], ['prompt' => '', 'value'=>'MALE']) ?>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-3">
                                <?= $form->field($model, 'user_type')->dropdownList([
                                        'MILITARY'=>'  Military  ',
                                        'CIVIL'=>'  Civil  ',
                                        'FOREGN'=>'   Foreign  ',
                                ],['prompt'=>'Select User Type'])->label('User Type')  ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'nationality')->dropDownList(ArrayHelper::map(\backend\models\Nationality::find()->all(),'id','name'), 
                                    ['prompt' => 'Select Nationality']) 

                                ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'dob')->widget(DatePicker::className(),[
                                    'pluginOptions'=>[
                                        'autoClose'=>true,
                                        'format'=>'yyyy-m-dd',
                                    ],
                                ])->label('Date of Birth(G.C)')?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'dob_ET')->widget(DatePicker::className(),[
                                    'pluginOptions'=>[
                                        'autoClose'=>true,
                                        'format'=>'yyyy-m-dd',
                                    ],
                                ])->label('Date of Birth(E.C)')?>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-4">
                                <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-3">
                                <?= $form->field($model, 'phone_mobile')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-3 hidden">
                                <?= $form->field($model, 'id')->textInput(['maxlength' => true]) ?>
                            </div>
                            <div class="col-md-5">
                                <div class="form-group pull-right">
                                    <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Save and continue') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">


    $("#modelUser input,select").change(function(e){
        $('#modelUser .btn').prop('disabled',false);
    });
    $("#modelUser").on('submit',function(e){
        e.preventDefault();
        ak = submitMaster( $("#modelUser")[0].id);
        if (ak == 1) {
            $('#modelUser .btn').prop('disabled',true);
        }
        return false;
    });
</script>