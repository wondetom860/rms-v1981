<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ContAssessCatagory2 */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cont-assess-catagory2-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'cat_catagory_relId')->dropdownList(\yii\helpers\ArrayHelper::map(\backend\models\CatagoryName2::find()->all(),'id','catagory_name'),['readonly'=>true]) ?>

    <?= $form->field($model, 'cat_maxPoint')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
