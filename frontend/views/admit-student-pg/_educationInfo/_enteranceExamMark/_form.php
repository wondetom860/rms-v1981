<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;

$modelEEM = &$sessionForm['models']['modelEI']['modelEEM'];

/** @var yii\web\View $this */
/* @var $model backend\models\EnteranceExamMark */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="enterance-exam-mark-form">

    <?php $form = ActiveForm::begin(['id'=>'modelEEM']); ?>

    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper_EEM', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-eems', // required: css class selector
        'widgetItem' => '.eem', // required: css class
        'limit' => 10, // the maximum times, an element can be cloned (default 999)
        'min' => 1, // 0 or 1 (default 1)
        'insertButton' => '.add-eem', // css class
        'deleteButton' => '.remove-eem', // css class
        'model' => $modelEEM[0],
        'formId' => 'modelEEM',
        'formFields' => [
            'eem_subject',
            'eem_reference_number',
            'eem_year',
            'eem_markObtained',
        ],
    ]); ?>

    <div class="container-eems"><!-- widgetContainer -->
    <?php foreach ($modelEEM as $i => $modelEEM1): ?>
        <div class="eem panel panel-default"><!-- widgetBody -->
            <div class="panel-heading">
                <h3 class="panel-title pull-left">EHEEE Subject Mark Information</h3>
                <div class="pull-right">
                    <button type="button" class="add-eem btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                    <button type="button" class="remove-eem btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                </div>
                <div class="clearfix"></div>
            </div>
            <div class="panel-body">
                <div class="row">
                    <?php
                        // necessary for update action.
                        if (! $modelEEM1->isNewRecord) {
                            echo Html::activeHiddenInput($modelEEM1, "[{$i}]id");
                        }
                        echo Html::activeHiddenInput($modelEEM[0], "[{$i}]eem_ei_relId",['class'=>'hidden_input_1']);
                    ?>
                    <div class="col-md-3">
                        <?= $form->field($modelEEM1, "[{$i}]eem_subject")->textInput(['maxlength' => true])->label('Subject') ?>
                    </div>
                    <div class="col-md-3">
                        <?= $form->field($modelEEM1, "[{$i}]eem_reference_number")->textInput(['maxlength' => true,['placeholder'=>'e.g. 2004']])->label('Reference Number') ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($modelEEM1, "[{$i}]eem_year")->textInput(['maxlength' => true,['placeholder'=>'e.g. 2011']])->label('Year (E.C)') ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($modelEEM1, "[{$i}]eem_markObtained")->textInput()->label('Mark Obtained') ?>
                    </div>  
                </div><!-- .row -->
            </div>
        </div>
    <?php endforeach; ?>
    </div>
    <?php DynamicFormWidget::end(); ?>

    <div class="form-group">
        <?= Html::submitButton($modelEEM[0]->isNewRecord ? Yii::t('decirs', 'Save and Continue') : Yii::t('decirs', 'Update'), ['class' => $modelEEM[0]->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
    $("#modelEEM input,select").change(function(e){
        $('#modelEEM .btn').prop('disabled',false);
    });
    $("#modelEEM").on('submit',function(e){
        e.preventDefault();
        ak = submitMaster($("#modelEEM")[0].id);
        if (ak == 1) {
            $(' #modelEEM .btn').prop('disabled',true);
        }
        return false;
    });
</script>
