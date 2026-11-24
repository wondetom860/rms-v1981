<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Reassessment\models\ReassessmentCommittee */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reassessment-committee-form">

    <?php $form = ActiveForm::begin(['id' => $model->formName()]); ?>
    <div class="row">
        <div class="col-md-12 hidden">
            <?= $form->field($model, 'ras_relId')->textInput() ?>
        </div>
        <div class="col-md-12">
            <?= $form->field($model, 'inst_relId')->dropDownList(ArrayHelper::map($insts, 'id', function ($model) {
                return $model->getFullName();
            }), ['prompt' => 'Choose Instructor']) ?>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success btn-sm pull-right', 'onclick' => 'submitForm();return false;']) ?>
            </div>
        </div>
    </div>
    <hr>
    <div class="row">
        <div class="col-md-12" id="member_list_container">
            Loading members...
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
<script>
    // $('form#<?= $model->formName() ?>').on('beforeSubmit', function(e) {
    //     var $form = $(this);
    //     $.post(
    //         $form.attr('action'),
    //         $form.serialize(),
    //         function(result) {
    //             if (result == 1) {
    //                 $form.reset;
    //                 loadMembers();
    //             } else {
    //                 alert(result);
    //             }
    //         });
    //     return false;
    // });
    const submitForm = () => {
        var $form = $('form#<?= $model->formName() ?>');
        $.post(
            $form.attr('action'),
            $form.serialize(),
            function(result) {
                if (result == 1) {
                    $form.reset;
                    loadMembers();
                } else {
                    alert(result);
                }
            });
        // return false;
    }
    const loadMembers = () => {
        $.post('/Reassessment/reassessment-history/list-committee-member', {
            ras_id: $('#reassessmentcommittee-ras_relid').val()
        }).done((resp) => {
            $('#member_list_container').html(resp);
        });
    }

    loadMembers();
</script>