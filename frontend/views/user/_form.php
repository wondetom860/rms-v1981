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

    <?php $form = ActiveForm::begin(); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Basic user information
                    </div>
                    <div class="panel-body">

                        <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'gender')->dropDownList([ 'MALE' => 'MALE', 'FEMALE' => 'FEMALE', ], ['prompt' => '', 'value'=>'MALE']) ?>

                        <?= $form->field($model, 'email')->textInput(['maxlength' => true]) ?>

                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-success">
                    <div class="panel-heading">
                        Basic user information
                    </div>
                    <div class="panel-body">

                         <?= $form->field($model, 'user_type')->dropdownList([
                                'MILITARY'=>'  Military  ',
                                'CIVIL'=>'  Civil  ',
                                'FOREGN'=>'   Foreign  ',
                        ])->label('User Type')  ?>

                        <?= $form->field($model, 'nationality')->dropDownList(ArrayHelper::map(\backend\models\Nationality::find()->all(),'id','name'), 
                            ['prompt' => 'Select Nationality']) 

                        ?>

                        <?= $form->field($model, 'dob')->widget(DatePicker::className(),[
                            'pluginOptions'=>[
                                'autoClose'=>true,
                                'format'=>'yyyy-m-dd',
                            ],
                        ])?>
                        <?= $form->field($model, 'phone_mobile')->textInput() ?>

                       <?= $form->field($model, 'userRole')->dropDownList($model->RoleTypes,[
                                'prompt'=>'Select Role'
                            ]) ?>

                    </div>
                    <div class="panel-footer">
                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Next >>') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success btn-sm ' : 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        
    </div>

    <?php ActiveForm::end(); ?>

</div>
