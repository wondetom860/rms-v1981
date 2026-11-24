<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\CourseAssessment\models\InstanceMarkAssignment */
/* @var $form yii\widgets\ActiveForm */

$reminaingPoints = $model->imaCatagorySetupRel->getRemainingWeight();
?>

<div class="instance-mark-assignment-form">

    <?php $form = ActiveForm::begin(['id' => $model->formName()]); ?>
    <div class="hidden">
        <?= $form->field($model, 'ima_catagorySetup_relId')->textInput() ?>
        <?= $form->field($model, 'old_ima_possiblePoint')->textInput() ?>
    </div>

    <?= $form->field($model, 'ima_instanceName')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'ima_possiblePoint')->textInput(['max' => $reminaingPoints, 'min' => 1, 'value' => $model->id ? $model->ima_possiblePoint : $reminaingPoints])->hint("Remaining Instance Weight: {$reminaingPoints}") ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>


<?php

$this->registerJs("

$('form#{$model->formName()}').on('beforeSubmit',function(e){
        var \$form = $(this);
        stdId = $('#addressinfo-std_relid').val();
        $.post(
            \$form.attr('action'),
            \$form.serialize(),
            function(result){
                //console.log(result);
                if(result == 1){
                    // $.pjax.reload({container:'#instance-mark-assignment-pjax'});
                    $('#modal').modal('hide');
                    showEvalutorsList(coff_det_id);
                }else{
                    alert(result);
                }
            });
        return false;
    });

", $this::POS_READY);

?>