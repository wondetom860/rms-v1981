<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\Admission */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="admission-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="container-fluid">
    	<div class="row">
    		<div class="col-md-6">
    			<div class="panel panel-primary">
	    			<div class="panel-heading">
	    				Add new Admission
	    			</div>
	    			<div class="panel-body">
	    				<?= $form->field($model, 'admission_name')->textInput(['maxlength' => true]) ?>
	    			</div>
	    			<div class="panel-footer">
					    <div class="form-group">
					        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
					    </div>
	    			</div>
	    		</div>
    		</div>
    		<div class="col-md-6">
    			<div class="panel panel-primary">
	    			<div class="panel-heading">
	    				Available Admissions
	    			</div>
	    			<div class="panel-body">
	    				<div class="container-fluid" id="admissionContainer">
	    					Loading...
	    				</div>
	    			</div>
	    			
	    		</div>
    		</div>
    	</div>
    </div>
    
    <?php ActiveForm::end(); ?>

</div>

<!-- admissionContainer -->

<?php
$this->registerJs(
    "
    window.loadAdmissions = function(){
    	$.post('/admission/get-admission',{},function(data){
    		$('#admissionContainer').html(data);
    		});
    }
    loadAdmissions();
    ",
    $this::POS_READY
);
?>

