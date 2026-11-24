<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View  $this
 * @var frontend\modules\AcStEva\models\AseEvaluationResult  $model
 * @var \backend\models\Instructor $inst
 * @var $form yii\widgets\ActiveForm 
 */

$criteriaCount = count(json_decode($model->criteriaBundleRel->criteria_IDS));
$inst = \backend\models\Instructor::findOne(['inst_user_relId' => $model->controlSettingRel->candidate_staff_relId]);
?>

<div class="ase-evaluation-result-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="hidden">
        <?= $form->field($model, 'control_setting_relId')->textInput() ?>

        <?= $form->field($model, 'criteria_bundle_relId')->textInput() ?>

        <?php //$form->field($model, 'completed_staffs')->textInput() 
        ?>

        <?= $form->field($model, 'dept_plan_relId')->textInput() ?>

        <?= $form->field($model, 'eval_point_rate')->textInput() ?>
    </div>

    <div class="row" style="font-size: 10pt; background-color: #FDD; padding: 5px;">
        <div class="col-md-6">
            Evaluation Type: <i><u><?= $model->criteriaBundleRel->evalTypeRel->type_name . " : {$model->criteriaBundleRel->evaluator_role} >> {$model->criteriaBundleRel->candidate_staff_role}" ?></u></i><br>
            Staff to be Evluated: <i><u><?= $model->controlSettingRel->candidateStaffRel->getFullName() ?></u></i><br>
            Staff Type: <i><u><?= $model->controlSettingRel->candidateStaffRel->user_type ?></u></i><br>
        </div>
        <div class="col-md-6">
            Employment Basis: <i><u><?= $inst->employmentTypeRel ? $inst->employmentTypeRel->et_name : 'Uknown' ?></u></i><br>
            Average percent : <i><u><?= sprintf("%0.2f", $model->criteriaBundleRel->average_percentage) . "%" ?></u></i><br>
            Course : <i><u><?= $model->criteriaBundleRel->evaluate_per_course ? $model->controlSettingRel->courseRel->getCourseCredits() : 'N/A' ?></u></i><br>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <h4>Evaluation Criteria</h4>
            <div style="max-height: 399px; overflow: auto;">
                <?= $this->render('__report/evaluation_criteria', ['model' => $model]) ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 bg-info" style="padding: 5px;">
            <div class="form-group bg-info">
                <p>
                    <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success btn-xs pull-right', 'onclick' => "submitMyForm(); return false;"]) ?>
                </p>
            </div>
        </div>
    </div>
    <br><br><br><br>

    <?php ActiveForm::end(); ?>

</div>


<script type="text/javascript">
    ratings = {};
    window.submitMyForm = () => {
        console.log(ratings);
        criteriaCount = Number($("#hjdhgdjk").text());
        if (Object.keys(ratings).length < criteriaCount) {
            alert('Please rate all criteria.');
            return;
        }
        if (confirm('Are you sure you want to save this evaluation?')) {
            $("#aseevaluationresult-eval_point_rate").val(JSON.stringify(ratings));
            document.forms[0].submit();
        }
    }
    window.rateScale = (scaleRate, point) => {
        // console.log('Rating: ' + scaleRate + ' for point: ' + point);
        $("." + point).removeClass('bg-success');
        $("." + point + "_radio").prop('checked', false);
        $("#_" + point + "_" + scaleRate).prop('checked', true);
        $("#" + point + "_" + scaleRate).addClass('bg-success');

        ratings[point] = scaleRate;

        console.log(ratings);
        $("#kgkdhjg").text(Object.keys(ratings).length);
        $("#aseevaluationresult-eval_point_rate").val(JSON.stringify(ratings));

    }
</script>