<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;
use yii\helpers\ArrayHelper;

$modelEEM = &$sessionForm['models']['modelEI']['modelEEM'];
$modelPSE = &$sessionForm['models']['modelEI']['modelPSE'];
/** @var yii\web\View $this */
/* @var $model backend\models\PostSecondaryEducation */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="post-secondary-education-form">

    <?php $form = ActiveForm::begin(['id'=>'modelPSE']); ?>

    <div class="row">
        <div class="col-md-9">
            Have you ever been enrolled in any post-secondary educational institution(s) (University or College level) in Ethiopia or abroad?
        </div>
        <div class="col-md-3">
            <?= $form->field($modelPSE[0], 'attendedPSE')->radioList(
                [
                    1=>' YES ',
                    0=>' NO ',
                ],
                [
                    'id'=>'attendedPSE_id',
                    'value'=>0,
                    'title'=>'Attended Post Secondary Education',
                    'onchange'=>'gettt();',
                ]
            )->label(false) ?>
        </div>
    </div>
    <div class="row" id="post-secondary-education"  style="display: none;">
        <div class="panel panel-default">
            <div class="panel-heading"><h4><i class="glyphicon glyphicon-envelope"></i> Post Secondary School</h4></div>
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
                        'pse_institute_name',
                        'pse_CGPA_obtained',
                        'pse_from_year',
                        'pse_to_year',
                        'pse_credit_earned',
                        'pse_certificate_earned',
                        'pse_nationality_relId',
                    ],
                ]); ?>

                <div class="container-pses"><!-- widgetContainer -->
                <?php foreach ($modelPSE as $i => $modelPSE1): ?>
                    <div class="pse panel panel-default"><!-- widgetBody -->
                        <div class="panel-heading">
                            <h3 class="panel-title pull-left">Post Secondary School</h3>
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
                                    echo Html::activeHiddenInput($modelPSE[0], "[{$i}]pse_ei_relId",['class'=>'hidden_input_1']);
                                ?>
                                <div class="col-md-3">
                                    <?= $form->field($modelPSE1, "[{$i}]pse_institute_name")->textInput()->label('Institute Name') ?>
                                </div>
                                <div class="col-md-3">
                                    <?= $form->field($modelPSE1, "[{$i}]pse_nationality_relId")->dropdownList(ArrayHelper::map(\backend\models\Nationality::find()->all(),'id','name'),
                                        [
                                            'prompt'=>'Seelct Country',
                                        ])->label('Country') ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($modelPSE1, "[{$i}]pse_from_year")->textInput(['maxlength' => true])->label('From Year') ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($modelPSE1, "[{$i}]pse_to_year")->textInput()->label('To Year') ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($modelPSE1, "[{$i}]pse_credit_earned")->textInput(['placeholder'=>'e.g. 85'])->label('Credit Earned') ?>
                                </div>
                                <div class="col-md-2">
                                    <?= $form->field($modelPSE1, "[{$i}]pse_CGPA_obtained")->textInput(['placeholder'=>'e.g. 3.98'])->label('CGPA') ?>
                                </div>  
                                <div class="col-md-3">
                                    <?= $form->field($modelPSE1, "[{$i}]pse_certificate_earned")->textInput()->label('Certificate Earned') ?>
                                </div>  
                            </div><!-- .row -->
                        </div>
                    </div>
                <?php endforeach; ?>
                </div>
                <?php DynamicFormWidget::end(); ?>
            </div>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($modelPSE[0]->isNewRecord ? Yii::t('decirs', 'Save and continue') : Yii::t('decirs', 'Update'), ['class' => $modelPSE[0]->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

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