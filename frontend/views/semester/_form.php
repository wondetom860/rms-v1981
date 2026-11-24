<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\Semester */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Adding new semester to the program.
                    </div>
                    <div class="panel-body">

                        <?= $form->field($model, 'semester_name')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'semester_desc')->textarea(['rows' => 6]) ?>

                        <?= $form->field($model, 'semester_status')->dropDownList(
                                    ['Active'], [
                                    'value'=>'Active', 'readonly'=>true, 'title'=>'By default the semester is active',
                                ]
                            ) ?>
                    </div>
                    <div class="panel-footer">
                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
