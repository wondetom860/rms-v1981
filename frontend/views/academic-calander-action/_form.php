<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\AcademicCalanderAction */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="academic-calander-action-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="container-fluid">
    	<div class="row">
    		<div class="col-md-6">
    			<div class="panel panel-primary">
    				<div class="panel-heading">
    					Add new College Schedule.
    				</div>
    				<div class="panel-body">
    					<?= $form->field($model, 'aca_name')->textInput(['maxlength' => true]) ?>
    					<?= $form->field($model, 'aca_link')->textInput(['maxlength' => true,'placeholder'=>'Optional']) ?>
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
    					Alredy exisiting Schedules
    				</div>
    				<div class="panel-body">
    					<div class="container-fluid" id="actionHistoryContainer">
    						
    					</div>
    				</div>
    				<div class="panel-footer">
    					@DEC
    				</div>
    			</div>
    		</div>
    	</div>
    </div>

    <?php ActiveForm::end(); ?>

</div>
<!-- actionHistoryContainer -->