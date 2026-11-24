<?php


use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\GraduationRequirmentControl */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="graduation-requirment-control-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'gr_program_relId')->dropdownList(
                ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
                [
                    'prompt'=>'Select Program',
                    'onchange'=>'loadSpecs()',
                ]
            ) ?>

    <?= $form->field($model, 'gr_maj')->textInput() ?>

    <?= $form->field($model, 'gr_min')->textInput() ?>

    

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
