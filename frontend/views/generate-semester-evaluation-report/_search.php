<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseOffering2DetailSearch */
/* @var $form yii\widgets\ActiveForm */
$inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->identity->id]);
// var_dump($inst);die();
?>

<div class="course-offering2-detail-search">

    <?php $form = ActiveForm::begin([
        'action' => ['index2'],
        'method' => 'post',
    ]); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'acadYear')->dropdownList(ArrayHelper::map(\backend\models\CourseOffering2Detail::find()->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')->groupBy('coff2_acadYear_relId')->all(), function($model){ return $model->coff2detCoff2Rel->coff2_acadYear_relId; } ,function($model){ return $model->coff2detCoff2Rel->coff2AcadYearRel->sch_year_desc; })) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'sem')->dropdownList(ArrayHelper::map(\backend\models\CourseOffering2Detail::find()->innerJoin('course_offering2','course_offering2.id = course_offering2_detail.coff2det_coff2_relId')->groupBy('coff2_sem_relId')->all(), function($model){ return $model->coff2detCoff2Rel->coff2_sem_relId; } ,function($model){ return $model->coff2detCoff2Rel->coff2SemRel->semester_name; })) ?>
        </div>

        <?php // echo $form->field($model, 'coff2det_TA_relId') ?>

        <?php // echo $form->field($model, 'coff2det_created_by') ?>

        <?php // echo $form->field($model, 'coff2det_created_at') ?>

        <?php // echo $form->field($model, 'coff2det_updated_at') ?>

        <?php // echo $form->field($model, 'coff2det_updated_by') ?>

        <div class="col-md-3">
            <div class="form-group"><br>
                <?= Html::submitButton(Yii::t('decirs', 'Search'), ['class' => 'btn btn-primary']) ?>
                <?= Html::resetButton(Yii::t('decirs', 'Reset'), ['class' => 'btn btn-default']) ?>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
