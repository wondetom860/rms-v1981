<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluationTypes */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-evaluation-types-form">

    <?php $form = ActiveForm::begin(); ?>

        <div class="container-fluid">
	    	<div class="row">
	    		<div class="col-md-6">
	    			<div class="panel panel-primary">
	    				<div class="panel-heading">
	    					Add new evaluation Type
	    				</div>
	    				<div class="panel-body">
	    					<?= $form->field($model, 'semev_type_desc')->textarea(['rows' => 3]) ?>

    						<?= $form->field($model, 'semev_type_level')->textInput() ?>

    						<?= $form->field($model, 'semev_type_maxPoint')->textInput() ?>

    						<!-- semev_type_maxPoint -->
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
	    					Available Evaluation Types
	    				</div>
	    				<div class="panel-body">
	    					<div class="container-fluid" id="evauationTypesContainer">
	    						Loading....
	    					</div>
	    				</div>
	    				<div class="panel-footer">
	    					
	    				</div>
	    			</div>
	    		</div>


	    	</div>
	    </div>

    <?php ActiveForm::end(); ?>
</div>

<!-- evauationTypesContainer -->
<?php

$this->registerJs(
	"
	window.loadEvalTypes = function(){
		$.post('/semester-evaluation-types/get-types',{},function(data){
			$('#evauationTypesContainer').html(data);
			});
	}

	loadEvalTypes();
	",
	$this::POS_READY
);

?>