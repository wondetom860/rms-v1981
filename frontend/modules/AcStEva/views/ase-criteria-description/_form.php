<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseCriteriaDescription */
/* @var $form yii\widgets\ActiveForm */
$model->category_name = $model->categoryRel->category_name;
?>

<div class="ase-criteria-description-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-8">
            <?= $form->field($model, 'category_name')->textInput(['readOnly' => true]) ?>
        </div><div class="col-md-4">
            <?= $form->field($model, 'code')->textInput(['maxlength' => true]) ?>
        </div><div class="col-md-9">
            <?= $form->field($model, 'description')->textarea(['rows' => 2]) ?>
        </div><div class="col-md-3">
            <?= $form->field($model, 'max_rating_scale')->textInput(['value' => 5]) ?>
        </div>
    </div>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
