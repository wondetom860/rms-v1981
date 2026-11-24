<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ContAssessCatagory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cont-assess-catagory-form">

    <?php $form = ActiveForm::begin(); ?>

        <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'cat_course_relId')->dropdownList(ArrayHelper::map($model->getDeptCourses(),'id','course_title'),['prompt'=>'Select Course'])?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'cat_name_relId')->dropdownList(
                ArrayHelper::map(\backend\models\ContAssessCatagoryName::find()->all(),'id','cont_cat_name')
                ,[
                    'prompt'=>'Select Catagory Name...',
                ]
            ) ?>
        </div>
         <div class="col-md-3">
            <?= $form->field($model, 'cat_maxPoint')->textInput() ?>
        </div>
        <div class="col-md-3"><br>
            <div class="form-group">
                <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Add') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
