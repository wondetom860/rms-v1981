<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\Reassessment\models\ReassessmentCommittee */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="reassessment-committee-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ras_relId')->textInput() ?>

    <?= $form->field($model, 'inst_relId')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
