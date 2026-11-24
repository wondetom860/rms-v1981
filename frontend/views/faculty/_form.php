<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\Faculty */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="faculty-form">

    <div class="row">
        <div class="col-md-12">
            <?php $form = ActiveForm::begin(['action'=>'/faculty/create']); ?>
            <div class="panel panel-primary">
                <div class="panel-heading">
                     Add new Fuculty
                </div>
                <div class="panel-body" style="background-color: #dd9;">
                    <div class="row">
                        <div class="col-md-4">
                            <?= $form->field($model, 'fac_name')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'fac_description')->textarea(['rows' => 2]) ?>
                        </div>
                        <div class="col-md-4"><br>
                            <div class="form-group">
                                <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

</div>
