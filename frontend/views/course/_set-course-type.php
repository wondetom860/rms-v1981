<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\Course */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="course-form">

    <?php $form = ActiveForm::begin(['action' => '/course/update?id=' . $model->id]); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        Course Type
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'course_type')
                            ->dropdownList(
                                $model->getCourseTypeOptios(),
                                [
                                    'prompt' => 'Course Entry Type',
                                ]
                            ) ?>
                    </div>
                </div>
                <div class="panel-footer">
                    <div class="form-group">
                        <?= Html::submitButton($model->isNewRecord ? Yii::t('rms', 'Create') : Yii::t('rms', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                    </div>
                </div>
            </div>

        </div>

    </div>

</div>

<?php ActiveForm::end(); ?>

</div>