<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WEvaluationTypes */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wevaluation-types-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="container-fluid">
    	<div class="row">
    		<div class="col-md-4">
    			<?= $form->field($model, 'et_name')->textInput(['maxlength' => true]) ?>

    		</div>
    		<div class="col-md-4">
    			<?= $form->field($model, 'et_per_course')->dropdownList(
    				[
    					1=>'YES',
    					0=>'NO',
    				],
    				['prompt'=>'---select---']) ?>
    		</div>
    		<div class="col-md-4">
    			
    			<?= $form->field($model, 'et_description')->textarea(['rows' => 3]) ?>

    		</div>
    	</div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decris', 'Create') : Yii::t('decris', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
