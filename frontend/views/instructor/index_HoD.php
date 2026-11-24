<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\Instructor */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="instructor-form">
 
    <?php $form = ActiveForm::begin(); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-5">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Instructor additional info
                    </div>
                    <div class="panel-body">
                        <?php echo Html::activeHiddenInput($model, "inst_user_relId",['value'=>$model->instUserRelId,'readOnly'=>TRUE,]);?>
                        <?= $form->field($model, 'instName')->textInput(['value'=>$model->getName($model->instUserRelId),'readOnly'=>TRUE,]) ?>

                        <?= $form->field($model, 'inst_dep_relId')->dropdownList(
                                                ArrayHelper::map(\backend\models\Department::find()->all(), 'id', 'dept_name'),
                                                ['prompt' => 'Select Department',]
                                            ) ?>

                        <?= $form->field($model, 'inst_acq_relId')->dropdownList(
                                                ArrayHelper::map(\backend\models\AcademicQualification::find()->all(), 'id', 'acq_name'),
                                                [
                                                    'prompt'=>'Select Academic Academic Qualification',
                                                    'onChange'=>'',
                                                ]
                                            ) ?>

                        <?= $form->field($model, 'inst_acr_relId')->dropdownList(
                                                ArrayHelper::map(\backend\models\AcademicRank::find()->all(), 'id', 'acr_name'),
                                                [
                                                    'prompt'=>'Select Academic Rank',
                                                    'onChange'=>'',
                                                ]
                                            ) ?>

                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Create') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>

                    </div>
                </div>
            </div>
        </div>
    </div>
    <?php ActiveForm::end(); ?>

</div>
