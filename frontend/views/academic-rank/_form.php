<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\AcademicRank */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="academic-rank-form">

    <?php $form = ActiveForm::begin(); ?>
    <div class="container-fluid">
        <div class="row">
            <div class="col-md-6">
                <div class="panel panel-primary">
                    <div class="panel-heading">
                        New academic rank Entry Form
                    </div>
                    <div class="panel-body">
                        <?= $form->field($model, 'acr_name')->textInput(['maxlength' => true]) ?>

                        <?= $form->field($model, 'acr_desc')->textarea(['rows' => 6]) ?>
                    </div>
                    <div class="panel-footer">
                        <div class="form-group">
                            <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Save and continue') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary']) ?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="panel panel-info">
                    <div class="panel-heading">
                        Available Academic Ranks
                    </div>
                    <div class="panel-body">
                        <div class="container" id="academicRnkContainer">
                            Loading academic ranks...
                        </div>
                    </div>
                    <div class="panel-footer">
                        
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<!-- academicRnkContainer -->
<?=
$this->registerJs(
    "
    $.post(
        '/academic-rank/ranks',
        {},
        function(data){
            $('#academicRnkContainer').html(data);
        });
    ",
    $this::POS_READY
);
?>
