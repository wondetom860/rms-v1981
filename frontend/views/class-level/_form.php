<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ClassLevel */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="class-level-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'clslvl_admission_relId')->dropdownList(
        ArrayHelper::map(\backend\models\Admission::find()->all(), 'id', 'admission_name'),
        [
            'option'=>'Select Admission',
            'onChange'=>' $.post( "'.Yii::$app->urlManager->createUrl('admission/programlists').'",
                {
                    admId:$(this).val()
                }, 
                function( data ) {
                console.log("Returned: "+data);
              //$( "select#coursecatalog-coff_clslvl_relid" ).html( data );
            });
           // console.log("Program Change made:");',
        ]
    ) ?>

    <?= $form->field($model, 'clslvl_program_relId')->dropdownList(
        ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
        [
            'option'=>'Select program',
            'onChange'=>' $.post( "'.Yii::$app->urlManager->createUrl('program/clslvllists').'",
                {
                    admId:$(this).val()
                }, 
                function( data ) {
                console.log("Returned: "+data);
              //$( "select#coursecatalog-coff_clslvl_relid" ).html( data );
            });
           // console.log("Program Change made:");',
        ]
    ) ?>


    <?= $form->field($model, 'clslvl_name')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'clslvl_num')->textInput(['maxlength'=>true,]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
