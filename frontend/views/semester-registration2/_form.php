<?php

use backend\models\ClassLevel;
use backend\models\SchoolYear;
use backend\models\Semester;
use frontend\modules\AcademicCalendar\models\AcAcademicYear;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\models\SemesterRegistration2 */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-registration2-form" style="font-size: 9pt;">

    <?php $form = ActiveForm::begin(); ?>
    <div class="hidden">
        <?= $form->field($model, 'semreg_std_relId')->textInput() ?>
    </div>
    <div class="row">
        <div class="col-md-6">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Name: <b><u><?= $model->semregStdRel->getFullName() ?></u></b>
                    Reg. ID: <b><u><?= $model->semregStdRel->std_regId ?></u></b>
                </div>
                <div class="panel-body">
                    <div class="row" style="background-color: #7D9;">
                        <div class="col-md-4">
                            <?= $form->field($model, 'semreg_acadYear_relId')
                                ->dropDownList(ArrayHelper::map(SchoolYear::find()->all(), 'id', function ($model) {
                                    return $model->sch_year_desc;
                                })) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'semreg_clslvl_relId')
                                ->dropDownList(ArrayHelper::map(ClassLevel::find()->where(['clslvl_program_relId' => $model->semregStdRel->std_prog_relId])->all(), 'id', function ($model) {
                                    return $model->clslvl_name;
                                })) ?>
                        </div>
                        <div class="col-md-4">
                            <?= $form->field($model, 'semreg_semester_relId')
                                ->dropDownList(ArrayHelper::map(Semester::find()->all(), 'id', function ($model) {
                                    return $model->semester_name;
                                })) ?>
                        </div>
                        <hr>

                        <div class="form-group pull-right">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success btn-xs' : 'btn btn-primary btn-xs', 'onclick' => "addSemregEntry();return false;"]) ?>
                        </div>
                    </div>
                    <div class="row" style="background-color: #7D9;" id="semregs_container">

                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="panel panel-secondary">
                <div class="panel-heading">
                    >>Please select registration entry
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>
    <script>
        window.addSemregEntry = () => {
            $.post('/semester-registration2/create-entry', {
                acadYearId: $("#semesterregistration2-semreg_acadyear_relid :checked").val(),
                stdId: $("#semesterregistration2-semreg_std_relid").val(),
                semId: $("#semesterregistration2-semreg_clslvl_relid :checked").val(),
                clslvlId: $("#semesterregistration2-semreg_semester_relid :checked").val,
            }).then((resp) => {
                if (resp == 1) {
                    laodSemregEntry();
                }
            });
        }

        window.laodSemregEntry = () => {

        }
    </script>
</div>