<?php

use backend\models\ClassLevel;
use backend\models\Student;
use backend\models\Student1;
use frontend\modules\AcademicCalendar\models\AcAcademicYear;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\TRANSTUD\models\TransferStudent */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="transfer-student-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $model->std_relId ? "" : $form->field($model, 'std_relId')->dropDownList(ArrayHelper::map($model->getTransferStudents(), 'id', 'full_name'), ['prompt' => '--Select Student--']) ?>

    <?= $form->field($model, 'transfered_from')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'year_admitted')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'sgpa')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'cgpa')->textInput(['maxlength' => true]) ?>

    <?= $form->field($model, 'year_transfered')->dropDownList(ArrayHelper::map(AcAcademicYear::find()->all(), 'id', function ($model) {
        return $model->getAcHeader();
    })) ?>

    <?= $form->field($model, 'class_level_joined')->dropDownList(ArrayHelper::map(
        ClassLevel::find()->all(),
        'id',
        function ($model) {
            return $model->clslvl_num . ' - ' . $model->clslvl_name . '' . ' (' . $model->clslvlProgramRel->getDegreeNomenclature() . ')';
        }
    )) ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>