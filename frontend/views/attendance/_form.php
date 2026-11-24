<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;
use yii\helpers\ArrayHelper;
use kartik\date\DatePicker;
/** @var yii\web\View $this */
/* @var $model frontend\models\Attendance */
/* @var $form yii\widgets\ActiveForm */
//$this->params['courseID']= $courseID;
//$ID=$courseID;
?>
<div class="attendance-form">

    <?php $form = ActiveForm::begin(); ?>

    <?php //echo $form->field($model, 'a_instructor_relId')->textInput() ?>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'a_createdAt')->widget(DatePicker::class,[
                'pluginOptions'=>[
                    'autoClose'=>true,
                    'format'=>'yyyy-m-dd',
                ],
            ]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'a_hour')->textInput(['max' => 3, 'min' => 1]) ?>
        </div>
    </div>
    <?php //echo $form->field($model, 'a_updatedAt')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton('Save', ['class' => 'btn btn-success']) ?>
    </div>
    <hr>

    <?php ActiveForm::end(); ?>

</div>
