<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ScholarshipType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="scholarship-type-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Add new scholarship type
                    </div>
                    <div class="panel-body">
                        
                    <?= $form->field($model, 'schType_name')->textInput(['maxlength' => true]) ?>

                    <?= $form->field($model, 'schType_pay')->dropDownList([
                        0=>'Free',
                        1=>'Pay / Fee required',
                    ],[
                        'prompt'=>'Select fee type'
                    ])?>

                    <?= $form->field($model, 'schType_by')->textInput(['maxlength' => true,'placeholder'=>'Bishoftu Automotive, Self-sponsored']) ?>

                    <?= $form->field($model, 'schType_desc')->textarea(['rows' => 6]) ?>

                    </div>
                    <div class="panel-footer">
                        
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Previously registered scholarship types
                    </div>
                    <div class="panel-body">
                        <div class="container-fluid" style="font-size: 10pt;" id="schTypeCont">
                            
                        </div>
                    </div>
                    <div class="panel-footer">
                        
                    </div>
                </div>
            </div>
        </div>        
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<!-- schTypeCont -->

<?php
$this->registerJs(
    "
    window.loadSchTypes = function(){
        $('#schTypeCont').html('Loading available Sponsor-Ship types.');
        $.post(
        '/scholarship-type/getschtypes',
        {},
        function(data){
                console.log(data);
                $('#schTypeCont').html(data);
            });
    }

    loadSchTypes();
    ",
    $this::POS_READY);

?>