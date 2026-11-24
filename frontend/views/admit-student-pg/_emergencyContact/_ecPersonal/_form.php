<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\EmergencyContact */
/* @var $form yii\widgets\ActiveForm */
$model = &$sessionForm['models']['modelEC'];
?>

<div class="emergency-contact-form">

    <?php $form = ActiveForm::begin(['id'=>'modelEC']); ?>

    <div class="panel panel-warning">
        <div class="panel-heading">
            Emergency Contact
        </div>
        <div class="panel-body">
            <div class="container-fluid">
                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($model, 'ec_first_name')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'ec_last_name')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'ec_relationShip')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'ec_phone_home')->textInput(['maxlength' => true]) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-3">
                        <?= $form->field($model, 'ec_phone_mobile')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($model, 'ec_email')->textInput(['maxlength' => true]) ?>
                    </div>
                    <div class="col-md-3"><br>
                        <div class="form-group" style="margin-top: 5px;">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Save and continue') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>


<script type="text/javascript">
    $("#modelEC input,select").change(function(e){
        $('#modelEC .btn').prop('disabled',false);
    });
    $("#modelEC").on('submit',function(e){
        e.preventDefault();
        ak = submitMaster( $("#modelEC")[0].id);
        if (ak == 1) {
            $('#modelEC .btn').prop('disabled',true);
        }
        return false;
    });
</script>
