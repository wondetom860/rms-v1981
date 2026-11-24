<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\FacultyCourseAssignment */
/* @var $form yii\widgets\ActiveForm */

// HoD
$inst = \backend\models\Instructor::findOne(['inst_user_relId' => Yii::$app->user->id]);
$assigneds = $model->getAssignedsId();
?>

<div class="faculty-course-assignment-form">

    <?php $form = ActiveForm::begin(); ?>

    <?= $form->field($model, 'inst_relId')->dropdownList(ArrayHelper::map(\backend\models\Instructor::find()->where(['inst_dep_relId' => $inst->inst_dep_relId])->andWhere(['NOT IN','id',$assigneds])->all(),'id',function($model){ return $model->getFullName(); })) ?>

    <?= $form->field($model, 'assign_as')->textInput(['rows' => 1]) ?>

    <?= $form->field($model, 'remark')->textInput() ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>
