<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\modules\SemesterEvaluation\models\WEvaluationPoints */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="wevaluation-points-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'ep_desc')->textarea(['rows' => 3]) ?>

    <div class="form-group">
        <?= Html::submitButton($model->isNewRecord ? Yii::t('decris', 'Create') : Yii::t('decris', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
