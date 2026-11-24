<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;

/** @var yii\web\View $this */
/* @var $model frontend\models\GraduationEligiblity */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="graduation-eligiblity-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Create New Graduation Eligiblity Controll - <b>By Admin</b>
                </div>
                <div class="panel-body">
                    <div class="row">

                        <div class="col-md-2">
                            <?= $form->field($model, 'ge_adm_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\Admission::find()->all(),'id','admission_name'),
                                [
                                    'prompt'=>'Select Admission',
                                ]) ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'ge_prog_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\Program::find()->all(),'id','program_name'),
                                [
                                    'prompt'=>'Select Program',
                                ]) ?>
                        </div>
                        <div class="col-md-2">
                            <?= $form->field($model, 'ge_num_of_F')->textInput() ?>
                                
                        </div>
                        <div class="col-md-2"><?= $form->field($model, 'ge_min_crdthr')->textInput() ?></div>
                        <div class="col-md-2"><?= $form->field($model, 'ge_min_CGPA')->textInput() ?></div>
                        <div class="col-md-2"><?= $form->field($model, 'ge_min_CGPA_major')->textInput() ?></div>
                                           
                    </div>
                    <div class="row">
                        <div class="col-md-12 bg-success">
                            <div class="form-group pull-right"><br>
                                <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                            </div>
                        </div>  
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
