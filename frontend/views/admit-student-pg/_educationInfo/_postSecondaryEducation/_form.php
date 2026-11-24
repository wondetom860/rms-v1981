<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;

$modelEEM = &$sessionForm['models']['modelEI']['modelEEM'];
$modelPSE = &$sessionForm['models']['modelEI']['modelPSE'];
/** @var yii\web\View $this */
/* @var $model backend\models\PostSecondaryEducation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="post-secondary-education-form">

    <div class="panel panel-danger">
        <div class="panel-heading" style="background-color: #8DF;">
            2.2. Post Secondary Education - COLLEGES
            <span class="bg-info" style="font-style: italic;">
                Write your overall educational background in form below
            </span>
        </div>
        <!-- 'action'=>'/post-secondary-education/create', -->
        <div class="panel-body">
            <?php $form = ActiveForm::begin(['id'=>'modelPSE']); ?>
            <div class="row" id="post-secondary-education">
                <div class="panel-body">
                    <?php DynamicFormWidget::begin([
                        'widgetContainer' => 'dynamicform_wrapper_PSE', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
                        'widgetBody' => '.container-pses', // required: css class selector
                        'widgetItem' => '.pse', // required: css class
                        'limit' => 10, // the maximum times, an element can be cloned (default 999)
                        'min' => 1, // 0 or 1 (default 1)
                        'insertButton' => '.add-pse', // css class
                        'deleteButton' => '.remove-pse', // css class
                        'model' => $modelPSE[0],
                        'formId' => 'modelPSE',
                        'formFields' => [
                            'pgpss_institute_name',
                            'pgpss_location',
                            'pgpss_deploma_awarded',
                            'pgpss_date_of_award',
                        ],
                    ]); ?>

                    <div class="container-pses"><!-- widgetContainer -->
                        <?php foreach ($modelPSE as $i => $modelPSE1): ?>
                            <div class="pse panel panel-default"><!-- widgetBody -->
                                <div class="panel-heading">
                                    <h3 class="panel-title pull-left">Post Secondary School - COLLEGE</h3>
                                    <div class="pull-right">
                                        <button type="button" title="Add more" class="add-pse btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                                        <button type="button" title="Remove" class="remove-pse btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                                    </div>
                                    <div class="clearfix"></div>
                                </div>
                                <div class="panel-body">
                                    <div class="row">
                                        <?php
                                            // necessary for update action.
                                            if (! $modelPSE1->isNewRecord) {
                                                echo Html::activeHiddenInput($modelPSE1, "[{$i}]id");
                                            }
                                            echo Html::activeHiddenInput($modelPSE[0], "[{$i}]ppss_pei_relId",['class'=>'hidden_input_1']);
                                        ?>
                                        <div class="col-md-3">
                                            <?= $form->field($modelPSE1, "[{$i}]pgpss_institute_name")->textInput()->label('Institute Name') ?>
                                        </div>
                                        <div class="col-md-3">
                                            <?= $form->field($modelPSE1, "[{$i}]pgpss_location")->textInput()->label('Location') ?>
                                        </div>
                                        <div class="col-md-3">
                                            <?= $form->field($modelPSE1, "[{$i}]pgpss_deploma_awarded")->textInput()->label('Deploma Awarded') ?>
                                        </div>  
                                        <div class="col-md-3">
                                            <?= $form->field($modelPSE1, "[{$i}]pgpss_date_of_award")->widget(DatePicker::className(),[
                                                'pluginOptions'=>[
                                                    'autoClose'=>false,
                                                    'format'=>'yyyy-m-dd',
                                                    ],
                                                ])->label('Date of award') ?>
                                        </div>
                                    </div><!-- .row -->
                                </div>
                            </div>
                        <?php endforeach; ?>
                    </div>
                    <?php DynamicFormWidget::end(); ?>
                </div>
            </div>
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group pull-right">
                        <?= Html::submitButton($modelPSE1->isNewRecord ? Yii::t('decirs', 'Save and Continue') : Yii::t('decirs', 'Update'), ['class' => $modelPSE1->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>

<script type="text/javascript">
    $("#modelPSE input,select").change(function(e){
        $('#modelPSE .btn').prop('disabled',false);
    });
    $("#modelPSE").on('submit',function(e){
        e.preventDefault();
        ak = submitMaster($("#modelPSE")[0].id);
        if (ak == 1) {
            $('#modelPSE .btn').prop('disabled',true);
        }
        return false;
    });
    window.gettt = function(){
        if($('#attendedPSE_id :checked').val() == 1){
            $('#post-secondary-education').show();
        }else{
            $('#post-secondary-education').hide();
        }
    }
</script>