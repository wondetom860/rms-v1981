<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcademicCalendar\models\AcAcademicYear */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="ac-academic-year-form">

    <?php

     // convert dates :
    $model->start_date = $model->start_date? date('d-m-Y',$model->start_date) : '';
    $model->end_date = $model->end_date? date('d-m-Y',$model->end_date) : '';

    $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-3">
            <?= $form->field($model, 'college_relId')->dropDownList(ArrayHelper::map(common\models\AuthOrg8n::find()->all(),'id',function($model){ return $model->abrvn.' '.$model->name; }), ['prompt' => '','readonly' => isset($model->id)]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'school_year')->dropDownList(ArrayHelper::map(backend\models\SchoolYear::find()->all(),'sch_year_desc','sch_year_desc'), ['prompt' => '','readonly' => isset($model->id)]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'semester')->dropDownList(ArrayHelper::map(backend\models\Semester::find()->all(),'semester_name','semester_name'), ['prompt' => '','readonly' => isset($model->id)]) ?>
        </div>
        <div class="col-md-3">
            <?= $form->field($model, 'season')->dropDownList([ 'SPRING' => 'SPRING', 'AUTUMN' => 'AUTUMN', 'SUMMER' => 'SUMMER', ], ['prompt' => '','readonly' => isset($model->id)]) ?>
        </div>
    </div>
    <div class="row">
        <div class="col-md-4">
            <?= $form->field($model, 'start_date')->widget(DatePicker::className(),[
                'pluginOptions'=>[
                    'autoClose'=>true,
                    'format'=>'dd-m-yyyy',
                ],
            ]) ?>
        </div>
        <div class="col-md-4">
            <?= $form->field($model, 'end_date')->widget(DatePicker::className(),[
                'pluginOptions'=>[
                    'autoClose'=>true,
                    'format'=>'dd-m-yyyy',
                ],
            ]) ?>
        </div>
        <div class="col-md-4">
            <div class="form-group"><br>
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
        
    </div>

    <?php ActiveForm::end(); ?>

</div>
