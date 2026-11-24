<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\EducationInformation */
/* @var $form yii\widgets\ActiveForm */
$model = &$sessionForm['models']['modelEI']['modelEI'];
?>

<div class="education-information-form">

    <div class="panel panel-danger">
        <div class="panel-heading" style="background-color: #8DF;">
            Education Information - Write Your Educational Information here
        </div>
        <!-- 'action'=>'/education-information/create', -->
        <div class="panel-body">
            <?php $form = ActiveForm::begin(['id'=>'modelEI']); ?>
                <div class="row">
                    <div class="col-md-4">
                        <?= $form->field($model, 'pgei_UG_university_name')->textInput() ?>
                    </div>
                    <div class="col-md-2">
                        <?= $form->field($model, 'pgei_CGPA')->textInput() ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'pgei_field_of_study')->textInput() ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-6">
                        <?= $form->field($model, 'pgei_major')->textInput() ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'pgei_minor')->textInput() ?>
                    </div>
                </div>

                <div class="row">   
                    <div class="col-md-6">
                        <?= $form->field($model, 'pgei_work_experience')->textarea(['rows' => 8,'placeholder'=>'State the kind of work you have been engaged in.']) ?>
                    </div>
                    <div class="col-md-6">
                        <?= $form->field($model, 'pgei_reserches')->textarea(['rows' => 8,'placeholder'=>'Give a particular of any relevant experience in research or industry. Give references to any published work you have done. Enclose if available..']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <?= $form->field($model, 'pgei_awards')->textarea(['rows' => 4,'placeholder'=>'State the type of awards you have in recognition to your merit.']) ?>
                    </div>
                </div>
                <div class="row">
                    <div class="col-md-12">
                        <div class="form-group pull-right">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Save and Continue') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            <?php ActiveForm::end(); ?>
        </div>
    </div>

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
