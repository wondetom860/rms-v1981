<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WEvaluationPointEntryTable */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wevaluation-point-entry-table-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="container-fluid">
    	<div class="row">
    		<div class="col-md-4">
    			<?= $form->field($model, 'epet_eet_relId')->dropdownList(\yii\helpers\ArrayHelper::map($model->listEETs(),'id','name_et'),['prompt'=>'--select--']) ?>
    		</div><div class="col-md-4">
    			<?= $form->field($model, 'epet_ep_relId')->dropdownList(\yii\helpers\ArrayHelper::map(\backend\modules\SemesterEvaluation\models\WEvaluationPoints::find()->all(),'id','ep_desc'),['prompt'=>'--select--']) ?>
    		</div><div class="col-md-4">
    			<?= $form->field($model, 'epet_max_point')->textInput() ?>
    		</div>
    	</div>
    </div>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decris', 'Create') : Yii::t('decris', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
