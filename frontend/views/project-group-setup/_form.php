<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\ProjectCourseSetup */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="project-course-setup-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'pcs_acadYear_relId')->textInput() ?>

    <?= $form->field($model, 'pcs_sem_relId')->textInput() ?>

    <?= $form->field($model, 'pcs_gset_relId')->textInput() ?>

    <?= $form->field($model, 'pcs_course_relId')->textInput() ?>

    <?= $form->field($model, 'pcs_pgName_relId')->textInput() ?>

    <?= $form->field($model, 'pcs_advisor')->textInput() ?>

    <?= $form->field($model, 'pcs_group_leader')->textInput() ?>

    <?= $form->field($model, 'pcs_title')->textarea(['rows' => 6]) ?>

    <?= $form->field($model, 'pcs_approved')->textInput() ?>

    <?= $form->field($model, 'pcs_approved_at')->textInput() ?>

    <?= $form->field($model, 'pcs_approved_by')->textInput() ?>

    <?= $form->field($model, 'pcs_created_at')->textInput() ?>

    <?= $form->field($model, 'pcs_created_by')->textInput() ?>

    <?= $form->field($model, 'pcs_updated_at')->textInput() ?>

    <?= $form->field($model, 'pcs_updated_by')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
