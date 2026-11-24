<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\EducationInformation */
/* @var $form yii\widgets\ActiveForm */
$model = &$sessionForm['models']['modelEI']['modelEI'];
?>

<div class="education-information-form">

    <?php $form = ActiveForm::begin(['id'=>'modelEI']); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <?= $form->field($model, 'ei_enteranceMarkPoint')->textInput()->label('Total Enterance Mark') ?>
            </div>
            <div class="col-md-3"><br>
                <div class="form-group" style="margin-top: 5px;">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Save and continue') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<script type="text/javascript">
    $("#modelEI input").change(function(e){
        $('#modelEI .btn').prop('disabled',false);
    });
    $("#modelEI").on('submit',function(e){
        e.preventDefault();
        ak = submitMaster($("#modelEI")[0].id);

        if (ak == 1) {
            $('#modelEI .btn').prop('disabled',true);
        }
        return false;
    });
</script>
