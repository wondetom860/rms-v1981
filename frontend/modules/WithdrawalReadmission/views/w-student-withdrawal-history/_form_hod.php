<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\WithdrawalReadmission\models\WStudentWithdrawalHistory */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wstudent-withdrawal-history-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="hidden">
        <?= $form->field($model, 'swh_acadYear_relId')->textInput() ?>

        <?= $form->field($model, 'swh_sem_relId')->textInput() ?>

        <?= $form->field($model, 'swh_std_relId')->textInput() ?>
    </div>
    <div class="row">
        <div class="col-md-9">
            <?= $form->field($model, 'swh_reason_relId')->dropdownList(ArrayHelper::map(\backend\models\WithdrawalReason::find()->all(),'id','withdrawalres_description'),['prompt' => 'Select witdrawal reason']) ?>
        </div>
        <div class="col-md-3"><br>
            <div class="form-group" style="margin-top: 7px;">
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <code class="hidden">
                <strong>Note: </strong>
                <ul>
                    <li>
                        Courses enrolled by the student for this semester will be removed after the last approval of the request.
                    </li>
                    <li>
                        Student withdrawal process initiated by HoD are presumed as withdrawal-by-deciplinary-dismissal.
                    </li>
                    <li>
                        Once the enrollment entries are removed, restoring back is impossible
                    </li>
                    <li>
                        The request is approved by the Registrar twice, the first is for confirmation and the later (which is the last approval) is for non-recoverable action. 
                    </li>
                </ul>
            </code>
        </div>
        <div class="col-md-12" id="courses_enrolled">
            <?= $this->render('__report/enrolled_courses',['model' => $modelSemreg]) ?>
        </div>
    </div>
    
    

    <?php ActiveForm::end(); ?>

</div>
