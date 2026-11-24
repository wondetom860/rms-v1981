<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\AcademicQualification */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="academic-qualification-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Register new Academic Qualification
                    </div>
                    <div class="panel-body">
                        
                        <?= $form->field($model, 'acq_name')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'acq_desc')->textarea(['rows' => 6]) ?>
                    </div>
                    <div class="panel-footer">
                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Save and continue') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
