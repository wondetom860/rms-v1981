<?php

use backend\models\CatagoryName2;
use backend\models\ContAssessCatagoryName;
use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\CourseAssessment\models\ContAssessCatagory */
/* @var $form yii\widgets\ActiveForm */

$remainingPoints = $model->getRemainingPoint();
$nameIds = $model->getCatNameIds();

$catNames = ($model->id || empty($nameIds)) ?
    CatagoryName2::find()->all() :
    CatagoryName2::find()->where(" id NOT IN (" . implode(',', array_values($nameIds)) . ") ")->all();
?>

<div class="cont-assess-catagory-form">

    <?php $form = ActiveForm::begin(['id' => $model->formName()]); ?>
    <div class="hidden">
        <?= $form->field($model, 'cat_acadYear_relId')->textInput() ?>

        <?= $form->field($model, '_old_cat_maxPoint')->textInput() ?>

        <?= $form->field($model, 'cat_sem_relId')->textInput() ?>

        <?= $form->field($model, 'cat_coff_relId')->textInput() ?>

        <?= $form->field($model, 'cat_course_relId')->textInput() ?>

        <?= $form->field($model, 'cat_created_at')->textInput() ?>

        <?= $form->field($model, 'cat_created_by')->textInput() ?>

        <?= $form->field($model, 'cat_updated_by')->textInput() ?>

        <?= $form->field($model, 'cat_updated_at')->textInput() ?>
    </div>


    <?= $form->field($model, 'cat_catagory_relId')->dropDownList(ArrayHelper::map($catNames, 'id', 'catagory_name'), ['prompt' => '--choose type--']) ?>

    <?= $form->field($model, 'cat_maxPoint')->textInput()->hint("Remaining: {$remainingPoints}") ?>

    <div class="form-group">
        <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs("

$('form#{$model->formName()}').on('beforeSubmit',function(e){
        var \$form = $(this);
        stdId = $('#addressinfo-std_relid').val();
        $.post(
            \$form.attr('action'),
            \$form.serialize(),
            function(result){
                //console.log(result);
                if(result == 1){
                    // $.pjax.reload({container:'#instance-mark-assignment-pjax'});
                    $('#modal').modal('hide');
                    showEvalutorsList(coff_det_id);
                }else{
                    alert(result);
                }
            });
        return false;
    });

", $this::POS_READY);

?>