<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\select2\Select2;

/** @var yii\web\View $this */
/* @var $model backend\models\FacultyStaff */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="faculty-staff-form">

    <?php $form = ActiveForm::begin(); ?>


    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'fas_faculty_relId')->dropdownList(
                ArrayHelper::map(\backend\models\Faculty::find()->all(),'id','fac_name'),
                [
                    'prompt'=>'Select Fuculty',
                ]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'fas_roleName')->dropdownList(
                \common\models\User::getRoleTypes2(),
                [
                    'prompt'=>'Select Role',
                ]) ?>
        </div>
        <div class="col-md-3">

            <?= $form->field($model, 'fas_user_relId')->widget(Select2::classname(), [
                'data' => ArrayHelper::map(\common\models\User::getUsers(), 'id', 'fullName'),
                'options' => [
                    'placeholder' => 'Select user',
                ],
                'pluginOptions' => [
                    'allowClear' => true
                      ],
                  ]); 
                ?>
        </div>
        <div class="col-md-3">
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    </div>
    

    

    

    

    <?php ActiveForm::end(); ?>

</div>
