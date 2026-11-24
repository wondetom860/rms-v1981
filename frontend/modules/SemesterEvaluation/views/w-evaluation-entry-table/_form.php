<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WEvaluationEntryTable */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wevaluation-entry-table-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'eet_et_relId')->dropdownList(\yii\helpers\ArrayHelper::map(\backend\modules\SemesterEvaluation\models\WEvaluationTypes::find()->all(),'id','et_name'),['prompt'=>'---select---']) ?>

            </div><div class="col-md-3">
                <?= $form->field($model, 'eet_evaluator_role')->dropdownList(\common\models\User::getRoleTypes(),['prompt'=>'--select--']) ?>

            </div><div class="col-md-3">
                <?= $form->field($model, 'eet_reviewee_role')->dropdownList(\common\models\User::getRoleTypes(),['prompt'=>'--select--']) ?>

            </div><div class="col-md-3">
                <?= $form->field($model, 'eet_max_point')->textInput() ?>
            </div>
        </div>
    </div>


    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decris', 'Create') : Yii::t('decris', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    window.getReviweeRole = function(evaluatorRole){
        $.post('/user/roles',{evRole:evaluatorRole}).done(function(data){
            $('#wevaluationentrytable-eet_reviewee_role').html(data);
        })
    }
</script>
