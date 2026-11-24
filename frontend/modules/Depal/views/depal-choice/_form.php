<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Depal\models\DepalChoice */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="depal-choice-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="hidden">
        <?= $form->field($model, 'std_relId')->textInput() ?>

        <?= $form->field($model, 'setting_relId')->textInput() ?>

        <?= $form->field($model, 'dept_selected')->textInput() ?>
    </div>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-7">
                <h4>Student Information</h4>
                <h5><b>Full Name :</b> <?= $model->stdRel->getFullName() ?></h5>
                <h5><b>Control Setting Entry :</b> <?= $model->settingRel->getSettingHeader() ?></h5>
                <h5><b>Department Chose :</b> <?= $model->stringifyDepartments() ?></h5>
                <h5><b>CGPA :</b> <?= $model->getCGPA($model->settingRel->sectionRel,$model->stdRel) ?></h5>
                <h5><b>Categorized Under :</b> <?= $model->getCategorizedUnder($model->stdRel,$model->settingRel) ?></h5>
            </div>
            <div class="col-md-5">
                <?= $form->field($model, 'secured_dept_relId')->dropdownList(ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_code',['prompt' => 'Alloted Department'])) ?>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
