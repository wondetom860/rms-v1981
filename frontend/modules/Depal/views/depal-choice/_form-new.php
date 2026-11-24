<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Depal\models\DepalChoice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="depal-choice-form">

    <?php $form = ActiveForm::begin(['action' => $model->id ? "/Depal/depal-choice/update?id={$model->id}" : "/Depal/depal-choice/create"]); ?>
    <div class="hidden">
        <?= $form->field($model, 'std_relId')->textInput() ?>

        <?= $form->field($model, 'setting_relId')->textInput() ?>

        <?= $form->field($model, 'dept_selected')->textInput() ?>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5">
                <h4>Student Information</h4>
                <h5><b>Full Name :</b> <br>&nbsp;&nbsp;&nbsp;<u><?= $model->stdRel->getFullName() ?></u></h5>
                <h5><b>Control Setting Entry :</b> <br>&nbsp;&nbsp;&nbsp;<u><?= $model->settingRel->getSettingHeader() ?></u></h5>
                <h5><b>CGPA :</b> <br>&nbsp;&nbsp;&nbsp;<u><?= $model->getCGPA($model->settingRel->sectionRel,$model->stdRel) ?></u></h5>
                <h5><b>Categorized Under :</b> <br>&nbsp;&nbsp;&nbsp;<u><?= $model->getCategorizedUnder($model->stdRel,$model->settingRel) ?></u></h5>
                <h5><b>Max. # of departmetns to choose :</b> <br>&nbsp;&nbsp;&nbsp;<u><?= $model->settingRel->depts_count ?></u></h5>
                <h5><b>Active Schedule :</b> <br>&nbsp;&nbsp;&nbsp;<u><?= $model->settingRel->getScheduleInterval() ?></u></h5>
                <h5><b>Department Chose :</b> <br>&nbsp;&nbsp;&nbsp;<u><?= $model->stringifyDepartments() ?></u></h5><hr>
                Your Choice : <span style="font-style: italic; font-weight: bold;" id="choices">-</span>
                <span style="font-size: 8pt;"><br>&nbsp;&nbsp;&nbsp;&nbsp; Order: [<i>1<sup>st</sup>,2 <sup>nd</sup>, 3 <sup>rd</sup></i>...]</span>
            </div>
            <div class="col-md-7" style="font-size: 0.98em">
                <?= $this->render('__report/list-depts-for-choice',['settingModel' => $model->settingRel]) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success btn-xs']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
