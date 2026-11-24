<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/* @var $this yii\web\View */
/* @var $model frontend\modules\ExitExam\models\EtElligiblityTest */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="et-elligiblity-test-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="row hidden">
        <div class="col-md-6">
            <?= $form->field($model, 'semreg_relId')->textInput(['maxlength' => true]) ?>
            <?= $form->field($model, 'srd2_relId')->textInput(['maxlength' => true]) ?>
        </div>
    </div>
    <?= $form->field($model, 'semreg_relId')->textInput() ?>

    <?= $form->field($model, 'reason')->textInput() ?>

    <?= $form->field($model, 'srd2_relId')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
