<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluationPoints */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-evaluation-points-form">

    <?php $form = ActiveForm::begin(['id'=>$model->formName()]); ?>
    <div class="container-fluid">
    	<div class="row">
    		<div class="col-md-6">
    			<div class="panel panel-primary">
    				<div class="panel-heading">
    					Add new evaluation points
    				</div>
    				<div class="panel-body">
                        <?= $form->field($model, 'semevPoint_semev_relId')->dropDownList(ArrayHelper::map(\backend\models\SemesterEvaluationTypes::find()->all(),'id','semev_type_desc'),[
                            'prompt'=>'Select Evaluation Type',
                            'onChange'=>'loadEvalPoints($(this).val())',
                        ])?>

    					<?= $form->field($model, 'sem_eval_point_desc')->textarea(['rows' => 3]) ?>

    					<?= $form->field($model, 'remark')->textarea(['rows' => 3]) ?>
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
    					Available Evaluation Points
    				</div>
    				<div class="panel-body">
    					<div class="container-fluid" id="evauationPointsContainer">
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

<!-- evauationPointsContainer -->

<?php
$this->registerJs(
    "
    evalType = null;

    $('form#{$model->formName()}').on('beforeSubmit',function(e){
        var \$form = $(this);

        $.post(
        \$form.attr('action'),
        \$form.serialize()
        ).done(function(data){
            if(data == 1){
                \$form.trigger('reset');
                loadEvalPoints();
            }else{
                $('#evauationPointsContainer').html(data);
            }
            });
            return false; 
        })
    window.loadEvalPoints = function(evalType){
        $.post('/semester-evaluation-points/get-points',{evalType:evalType},function(data){
                $('#evauationPointsContainer').html(data);
            });
    }

    loadEvalPoints();
    ",
    $this::POS_READY);
?>
