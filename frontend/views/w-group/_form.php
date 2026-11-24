<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\WGroup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wgroup-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="container-fluid">
    	<div class="row">
    		<div class="col-md-6">
    			<div class="panel panel-primary">
    				<div class="panel-heading">
    					add new group
    				</div>
    				<div class="panel-body">
    					<?= $form->field($model, 'group_name')->textInput(['maxlength' => true]) ?>

    					<?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>
    				</div>
    				<div class="panel-footer">
    					<div class="form-group">
					        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary','id'=>'createGroup']) ?>
					    </div>

    				</div>
    			</div>
    		</div>
    		<div class="col-md-6">
    			<div class="panel panel-primary">
    				<div class="panel-heading">
    					Avaliable groups 
    				</div>
    				<div class="panel-body">
    					<div class="container-fluid" id="groupsContainer">
    						
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

<!-- groupsContainer -->
<?php
$this->registerJs(
    "
    
    ",
    $this::POS_READY
);
?>
