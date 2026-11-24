<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;
use kartik\date\DatePicker;

/** @var yii\web\View $this */
/* @var $model backend\models\User */
/* @var $form yii\widgets\ActiveForm */

$this->title = \Yii::t('rms', 'Create user account');
if (!\Yii::$app->user->isGuest) {
    $this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Users'), 'url' => ['index']];
}
$this->params['breadcrumbs'][] = Yii::t('rms', 'Signup');
?>

<div class="user-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="panel panel-primary">
                <div class="panel-heading">
                    Registration Page - Basic user information
                </div>
                <div class="panel-body">
                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($model, 'first_name')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'middle_name')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'last_name')->textInput(['maxlength' => true]) ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'gender')->dropDownList(['MALE' => 'MALE', 'FEMALE' => 'FEMALE',], ['prompt' => '', 'value' => 'MALE']) ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($model, 'user_type')->dropdownList([
                                'MILITARY' => '  Military  ',
                                'CIVIL' => '  Civil  ',
                                'FOREGN' => '   Foreign  ',
                            ])->label('User Type')  ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'nationality')->dropDownList(
                                ArrayHelper::map(\backend\models\Nationality::find()->all(), 'id', 'name'),
                                ['prompt' => 'Select Nationality']
                            )

                            ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'dob')->widget(DatePicker::className(), [
                                'pluginOptions' => [
                                    'autoClose' => true,
                                    'format' => 'yyyy-m-dd',
                                ],
                            ]) ?>
                        </div>

                        <div class="col-md-3">
                            <?= $form->field($model, 'userRole')->dropDownList($model->getRoleTypes(), ['prompt' => 'Select Role'])->label('User Role');
                            ?>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-md-3">
                            <?= $form->field($model, 'email')->textInput(['maxlength' => true])  ?>
                        </div>
                        <div class="col-md-3">
                            <?= $form->field($model, 'phone_mobile')->textInput(['maxlength' => true])

                            ?>
                        </div>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Save and continue') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>