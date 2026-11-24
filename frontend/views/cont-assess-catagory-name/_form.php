<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\Admission */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cont-assess-catagory-name-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="container-fluid">
    	<div class="row">
    		<div class="col-md-6">
    			<div class="panel panel-primary">
	    			<div class="panel-heading">
	    				Add new Category name
	    			</div>
	    			<div class="panel-body">
	    				    <?= $form->field($model, 'cont_cat_name')->textInput(['maxlength' => true]) ?>
	    			</div>
	    			<div class="panel-body">
	    				   <?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>
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
    
    <?php ActiveForm::end(); ?>

</div>


