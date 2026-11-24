<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;
use \frontend\modules\AcademicCalendar\models\AcAcademicYear;

/** @var yii\web\View  $this
 * @var frontend\modules\AcStEva\models\AseSemesterEvaluation $model
 * @var yii\widgets\ActiveForm  $form
 */
?>

<div class="ase-semester-evaluation-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="hidden">
        <?= $form->field($model, 'criteria_bundle_IDS')->textInput() ?>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'academic_year_relId')->dropdownList(ArrayHelper::map(AcAcademicYear::find()->where(['status' => AcAcademicYear::STATUS_ACTIVE])->all(), 'id', function ($model) {
                return $model->school_year . "-" . $model->semester;
            }), ['prompt' => 'Select Academic Year']) ?>
        </div>
        <div class="col-md-8">
            <?= $form->field($model, 'evaluation_header')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12" style="max-height: 410px; overflow-x: auto; border: 1px solid black;">
            <?= $this->render('../ase-criteria-bundle/__report/criteria-bundle-all', ['model' => $model]) ?></div>
        <div class="col-md-9">
            Selected Criteria : <span style="font-style: italic; font-weight: bold; font-size: 7pt;" id="choices_bundle">-</span>
        </div>
    </div>
    <div class="row">
        <div class="col-md-5">
            <?= $form->field($model, 'startdate')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'autoClose' => true,
                    'format' => 'dd-m-yyyy',
                ],
            ]) ?>
        </div>
        <div class="col-md-5">
            <?= $form->field($model, 'enddate')->widget(DatePicker::class, [
                'pluginOptions' => [
                    'autoClose' => true,
                    'format' => 'dd-m-yyyy',
                ],
            ]) ?>
        </div>
        <div class="col-md-2">
            <?= $form->field($model, 'force_above_half_evaluation')->checkbox()->hint('Above half Evaluation (>51%) is required for report generating') ?>
        </div>
        <!-- force_above_half_evaluation -->
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script type="text/javascript">
    __bundleList = [];
    document.addEventListener("DOMContentLoaded", function() {
        bun = $('#asesemesterevaluation-criteria_bundle_ids').val();
        //  ? JSON.parse(ex) : []
        try {
            console.log(JSON.parse(bun));
            __bundleList = JSON.parse(bun);
            $("#choices_bundle").text(JSON.stringify(findBundleCode(__bundleList)));
        } catch (err) {
            console.log(err);
        }
    });

    const addToBundleList = (bundleId) => {
        r = 0;
        if (jQuery.inArray(bundleId, __bundleList) !== -1) {
            // if item is already selected, remove it because user deselected it.
            __bundleList.splice($.inArray(bundleId, __bundleList), 1);
            r = 0;
        } else { //going to add the item to the list.
            __bundleList.push(bundleId);
            r = 1;
        }
        $('#asesemesterevaluation-criteria_bundle_ids').val(JSON.stringify(__bundleList));
        $('#bun__' + bundleId).prop({
            'checked': (r == 1)
        });
        $("#choices_bundle").text(JSON.stringify(findBundleCode(__bundleList)));
    }

    window.toggleDisplay = (id) => {
        $('#' + id).toggle('display');
    }

    window.findBundleCode = (__bundleList) => {
        code = [];
        $.each(__bundleList, (index, criteria) => {
            code.push($("#bun__" + criteria).data('code'));
            $('#bun__' + criteria).prop({
                'checked': true
            });
        });
        return code;
    }
</script>