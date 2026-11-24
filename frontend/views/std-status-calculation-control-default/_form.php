<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\StdStatusCalculationControlDefault */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="std-status-calculation-control-default-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Add New Status calculation Control
                    </div>
                    <div class="panel-body">

                        <div class="col-md-3">
                            <?= $form->field($model, 'sssccd_adm_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\Admission::find()->all(), 'id', 'admission_name'),
                                [
                                    'prompt' => 'Select Admission',

                                ]
                            ) ?>
                        </div>
                        <div class="col-md-3">

                            <?= $form->field($model, 'sssccd_prog_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
                                [
                                    'prompt' => 'Select Program',
                                    'onChange' => ' $.post("/class-level/classyearlists",
                                            {
                                                progId:$(this).val()
                                            }, 
                                            function( data ) {
                                            //console.log("Returned: "+data);
                                            $( "#stdstatuscalculationcontroldefault-sssccd_clslvl_relid" ).html( data );
                                        });
                                        // coffLoad();',
                                ]
                            ) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'sssccd_clslvl_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\ClassLevel::find()->all(), 'id', 'clslvl_name'),
                                [
                                    'prompt' => 'Select Class Level',
                                ]
                            ) ?>
                        </div>
                        <div class="col-md-3">
                            <!--  -->
                            <?= $form->field($model, 'sssccd_sem_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\Semester::find()->all(), 'id', 'semester_name'),
                                [
                                    'prompt' => 'Select Class Semester',
                                ]
                            ) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'sssccd_sssn_relId')->dropdownList(
                                ArrayHelper::map(\backend\models\StudentSemesterStatusName::find()->all(), 'id', 'sssn_name'),
                                [
                                    'prompt' => 'Select Class Semester status name',
                                ]
                            ) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'sssccd_min_CGPA')->textInput([
                                'min' => 0.00,
                                'max' => 4.00,
                                'scale' => 0.10,
                                'type' => 'double',
                                'title' => 'Min Point or this status',
                                'place-holder' => '2.00',
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'sssccd_max_CGPA')->textInput([
                                'max' => 4.00,
                                'min' => 0.00,
                                'type' => 'double',
                                'scale' => 0.10,
                                'title' => 'Max Point or this status',
                                'place-holder' => '4.00',
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'sssccd_min_SGPA')->textInput([
                                'min' => 0.00,
                                'max' => 4.00,
                                'scale' => 0.10,
                                'type' => 'double',
                                'title' => 'Min Point or this status',
                                'placeHolder' => 0.00,
                            ]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'sssccd_max_SGPA')->textInput([
                                'max' => 4.00,
                                'min' => 0.00,
                                'scale' => 0.10,
                                'type' => 'double',
                                'title' => 'Max Point or this status',
                                'place-holder' => '4.00',
                            ]) ?>
                        </div>
                        <div class="col-md-6">
                            <?= $form->field($model, 'sssccd_remark')->textarea(['rows' => 1]) ?>
                        </div>
                        <div class="col-md-2">
                            <div class="form-group"><br>
                                <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success pull-right' : 'btn btn-primary pull-right']) ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>

<!-- calculationControlContainer -->

<?php

$this->registerJs(
    "

    ",
    $this::POS_READY
);

?>