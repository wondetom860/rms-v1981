<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\MakeupExamReason */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="makeup-exam-reason-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="container-fluid">
    	<div class="row">
    		<div class="col-md-6">
    			<div class="panel panel-primary">
    				<div class="panel-heading">
    					Add new makeup exam reason
    				</div>
    				<div class="panel-body">
    					<?= $form->field($model, 'mer_desc')->textarea(['rows' => 6]) ?>

    					<?= $form->field($model, 'remark')->textarea(['rows' => 6]) ?>
    					
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
    					Available makeup exam reasons
    				</div>
    				<div class="panel-body">
    					<div class="container-fluid" id="MakeupExamReasonsContainer">
    						
    					</div>
    				</div>
    				<div class="panel-footer">
    					@rms
    				</div>
    			</div>
    		</div>
    	</div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<!-- MakeupExamReasonsContainer -->

<?=
$this->registerJs(
    "
    window.loadMER = function(){
        $.post('/makeup-exam-reason/merlists',{},function(data){
                $('#MakeupExamReasonsContainer').html(data);
            });
    }
    window.loadInst = function(deptId){
        $.post(
        '/department-head/inistlists',
        {deptId:deptId},
        function(data){
            console.log(data);
            $('#departmenthead-dh_inst_relid').html(data);
            });
    }
    loadMER();
    ",
    $this::POS_READY
);
?>