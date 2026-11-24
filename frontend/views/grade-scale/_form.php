<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\GradeScale */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="grade-scale-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Add new grade scale detail
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'gs_min')->textInput(['placeholder'=>'84.00']) ?>

                        <?= $form->field($model, 'gs_max')->textInput(['placeholder'=>'84.5']) ?>

                        <?= $form->field($model, 'gs_ltr_grade')->textInput(['maxlength' => true,'placeholder'=>'B+']) ?>

                        <?= $form->field($model, 'gs_gradePoint')->textInput(['placeholder'=>'3.50']) ?>
                        
                        <?= $form->field($model, 'gs_pass_status')->textInput(['placeholder'=>'1']) ?>

                        <?= $form->field($model, 'gs_scale_status')->checkBox() ?>
        	            <?= $form->field($model, 'gs_clearable')->dropdownList(
                                    [
                                        1=>'Yes',
                                        0=>'No',
                                    ],['prompt'=>'Grade clearable?']) ?>
                        <?= $form->field($model, 'gs_remark')->textarea(['rows' => 1]) ?>
                    </div>
                    <div class="panel-footer">

                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
