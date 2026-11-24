<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\StudentType */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="student-type-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Add new student Type
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'stdType_name')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'stdType_desc')->textarea(['rows' => 6]) ?>
                    </div>
                    <div class="panel-footer">
                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-4">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Available Studet Type record
                    </div>
                    <div class="panel-body">
                        <div class="container" id="studentTypeList">
                            Loading student type list....
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
