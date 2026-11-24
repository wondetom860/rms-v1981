<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\Program */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="program-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="container">
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Manage Program (Degree Level)
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'program_adm_relId')->dropDownList(ArrayHelper::map(\backend\models\Admission::find()->all(), 'id', 'admission_name'), ['prompt' => 'Select Admission']) ?>

                        <?= $form->field($model, 'program_name')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'program_duration')->textInput() ?>

                        <?= $form->field($model, 'program_prefix')->textInput() ?>

                        <?= $form->field($model, 'total_crdt_hrs')->textInput() ?>

                        <?= $form->field($model, 'program_desc')->textarea(['rows' => 6]) ?>
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

    <!-- <?= $form->field($model, 'program_created_by')->textInput() ?>

    <?= $form->field($model, 'program_created_at')->textInput() ?>

    <?= $form->field($model, 'program_updated_by')->textInput() ?>

    <?= $form->field($model, 'program_updated_at')->textInput() ?> -->

    <?php ActiveForm::end(); ?>

</div>