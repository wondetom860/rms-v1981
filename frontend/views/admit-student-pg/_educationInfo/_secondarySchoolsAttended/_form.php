<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use wbraganca\dynamicform\DynamicFormWidget;

$modelSSA = &$sessionForm['models']['modelEI']['modelSSA'];

// echo "<pre>";
// var_dump($modelSSA);
// echo "</pre>";
// return;

/**
 * This is the model class for table "pg_secondary_schools_attended".
 *
 * @property integer $id
 * @property integer $pssa_pei_relId
 * @property string $pgssa_name_of_school
 * @property string $pgssa_location
 * @property string $pgssa_years_from
 * @property string $pgssa_years_to
 *
 * @property PgEducationInfo $pssaPeiRel
 */

/** @var yii\web\View $this */
/* @var $model backend\models\SecondarySchoolAttended */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="secondary-school-attended-form">

    <?php $form = ActiveForm::begin(['id'=>'modelSSA']); ?>

    <?php DynamicFormWidget::begin([
        'widgetContainer' => 'dynamicform_wrapper_SSA', // required: only alphanumeric characters plus "_" [A-Za-z0-9_]
        'widgetBody' => '.container-ssass', // required: css class selector
        'widgetItem' => '.ssas', // required: css class
        'limit' => 10, // the maximum times, an element can be cloned (default 999)
        'min' => 1, // 0 or 1 (default 1)
        'insertButton' => '.add-ssas', // css class
        'deleteButton' => '.remove-ssas', // css class
        'model' => $modelSSA[0],
        'formId' => 'modelSSA',
        'formFields' => [
            'pgssa_school_name',
            'pgssa_town',
            'pgssa_year',
            'pgssa_gradeLevels',
        ],
    ]); ?>

    <div class="container-ssass"><!-- widgetContainer -->
        <?php foreach ($modelSSA as $i => $modelSSA1): ?>
            <div class="ssas panel panel-default"><!-- widgetBody -->
                <div class="panel-heading">
                    <h3 class="panel-title pull-left">Secondary Schools Attended</h3>
                    <div class="pull-right">
                        <button type="button" title="Add more" class="add-ssas btn btn-success btn-xs"><i class="glyphicon glyphicon-plus"></i></button>
                        <button type="button" title="Remove" class="remove-ssas btn btn-danger btn-xs"><i class="glyphicon glyphicon-minus"></i></button>
                    </div>
                    <div class="clearfix"></div>
                </div>
                <div class="panel-body">
                    <div class="row">
                        <?php
                            // necessary for update action.
                            if (! $modelSSA1->isNewRecord) {
                                echo Html::activeHiddenInput($modelSSA1, "[{$i}]id");
                            }

                            echo Html::activeHiddenInput($modelSSA1, "[{$i}]pssa_pei_relId",['class'=>'hidden_input_1']);
                        ?>
                        <div class="col-md-3">
                            <?= $form->field($modelSSA1, "[{$i}]pgssa_name_of_school")->textInput(['maxlength' => true])->label('School Name') ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($modelSSA1, "[{$i}]pgssa_location")->textInput(['maxlength' => true,['placeholder'=>'e.g. Wolkite']])->label('Town') ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($modelSSA1, "[{$i}]pgssa_years_from")->textInput(['maxlength' => true,['placeholder'=>'e.g. 2011']])->label('Year (E.C)') ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($modelSSA1, "[{$i}]pgssa_years_to")->textInput(['maxlength' => true,['placeholder'=>'e.g. 2011']])->label('Year (E.C)') ?>
                        </div>
                        
                    </div><!-- .row -->
                </div>
            </div>
        <?php endforeach; ?>
        </div>
    <?php DynamicFormWidget::end(); ?>

    <div class="form-group">
        <?= Html::submitButton($modelSSA[0]->isNewRecord ? Yii::t('decirs', 'Save and continue') : Yii::t('decirs', 'Update'), ['class' => $modelSSA[0]->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">

    $("#modelSSA input,select").change(function(e){
        $('#modelSSA .btn').prop('disabled',false);
    });
    $("#modelSSA").on('submit',function(e){
        e.preventDefault();
        $ak = submitMaster($("#modelSSA")[0].id);
        if ($ak == 1) {$("#modelSSA .btn").prop('disabled',true);}
        return false;
    });
</script>
