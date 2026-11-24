<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluationScale */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-evaluation-scale-form">

    <?php $form = ActiveForm::begin(); ?>

        <div class="container-fluid">
            <div class="row">
                <div class="col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Add new evaluation Scale
                        </div>
                        <div class="panel-body">
                            <?= $form->field($model, 'ses_name')->textInput(['maxlength' => true]) ?>

                            <?= $form->field($model, 'ses_maxPoint')->textInput() ?>

                            <?= $form->field($model, 'ses_minPoint')->textInput() ?>

                            <?= $form->field($model, 'ses_pass_status')->dropdownList([1=>'PASS',0=>'FAIL'],['prompt' => '']) ?>

                            <?= $form->field($model, 'remark')->textarea(['rows' => 2]) ?>
                            <!-- semev_type_maxPoint -->
                        </div>
                        <div class="panel-footer">
                            <div class="form-group">
                                <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Save') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                            </div>

                        </div>
                    </div>
                </div>

                <div class="col-md-6">
                    <div class="panel panel-primary">
                        <div class="panel-heading">
                            Available Semester Evaluation Result Scales
                        </div>
                        <div class="panel-body">
                            <div class="container-fluid" id="evauationTypesContainer">
                                Loading....
                            </div>
                        </div>
                        <div class="panel-footer">
                            
                        </div>
                    </div>
                </div>


            </div>
        </div>

    <?php ActiveForm::end(); ?>
</div>

<!-- evauationTypesContainer -->
<?php

$this->registerJs(
    "
    window.loadEvalTypes = function(){
        $.post('/semester-evaluation-scale/get-scales',{},function(data){
            $('#evauationTypesContainer').html(data);
            });
    }

    loadEvalTypes();
    ",
    $this::POS_READY
);

?>