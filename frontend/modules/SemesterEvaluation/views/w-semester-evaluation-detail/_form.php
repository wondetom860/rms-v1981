<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WSemesterEvaluationDetail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wsemester-evaluation-detail-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'sed_se_relId')->textInput() ?>

    <?= $form->field($model, 'sed_ep_relId')->textInput() ?>

    <?= $form->field($model, 'sed_point_given')->textInput() ?>

    <?= $form->field($model, 'data')->textarea(['rows' => 6]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decris', 'Create') : Yii::t('decris', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
