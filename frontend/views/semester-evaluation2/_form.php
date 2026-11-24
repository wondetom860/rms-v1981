<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\SemesterEvaluation2 */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="semester-evaluation2-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'semev_acadYear_relId')->textInput() ?>

    <?= $form->field($model, 'semev_sem_relId')->textInput() ?>

    <?= $form->field($model, 'semev_group_relId')->textInput() ?>

    <?= $form->field($model, 'semev_course_relId')->textInput() ?>

    <?= $form->field($model, 'semev_evaluee_relId')->textInput() ?>

    <?= $form->field($model, 'semev_evalType_relId')->textInput() ?>

    <?= $form->field($model, 'semev_averagePoint')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
