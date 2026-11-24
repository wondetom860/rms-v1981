<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model backend\models\CourseOffering2Detail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="course-offering2-detail-form">

    <?php $form = ActiveForm::begin(['id'=>'CourseOffering2Detail','action'=>$model->isNewRecord ? '/course-offering2-detail/create' : "/course-offering2-detail/update?id={$model->id}"]); ?>

    <div class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="container-fluid" style="display: none;">
                    <?= $form->field($model, 'coff2det_coff2_relId')->textInput() ?>

                    <?= $form->field($model, 'coff2det_cocat_relId')->textInput() ?>
                </div>

                <?= $form->field($model, 'coff2det_course_relId')->dropDownList(ArrayHelper::map(\backend\models\Course::find()->where(['id'=>$model->coff2det_course_relId])->all(),'id','course_title'),[
                    'readOnly'=>TRUE,
                    'title'=>'Assign Inst. and TA for this course',

                ]) ?>

              <div class="row" style="text-align: left;">
                  <div class="col-md-6">
                        <?php
                        if (is_null($model->HoDDept)) {
                            ?>
                            <?= $form->field($model, 'dept')->textInput([
                                'value'=>NULL,
                                'placeHolder'=>'TBA',
                                'readOnly'=>TRUE,
                                'title'=>'Instructor for this course is to be Assigned Later.',
                            ]) ?>
                            <?php
                        }else{
                            ?>
                        <div class="container-fluid" style="background-color: #ace;">
                            <?= $form->field($model, 'dept')->dropDownList(ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_name'),
                            [
                                        'prompt'=>'Select Dept for Instructor',
                                        'onchange'=>'$.post(
                                            "/instructor/instlist",
                                            {
                                                deptId:$(this).val()
                                            },
                                            function(data){
                                                // console.log(data);
                                                $("#courseoffering2detail-coff2det_inst_relid").html(data);
                                            })',
                                    ])->label('Filter Inst. by dept.') ?>

                            <?= $model->coff2det_inst_relId ? 
                                $form->field($model, 'coff2det_inst_relId')->dropDownList(ArrayHelper::map(\backend\models\Instructor::find()->all(),'id',function($model){ return $model->getFullName(); }),['prompt'=>'Select Instructor']) : $form->field($model, 'coff2det_inst_relId')->dropDownList([],[
                                'prompt'=>'Select Instructor',
                            ]) ?>
                        </div>
                            <?php
                        }
                        ?>
                  </div>
                  <div class="col-md-6">
                          <div class="container-fluid" style="background-color: #99E;">
                            <?php
                                $labHour = \backend\models\Course::findOne(['id'=>$model->coff2det_course_relId])->course_lab_hour;
                                if ($labHour > 0) {
                                    ?>
                                    <?= $form->field($model, 'dept2')->dropDownList(ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_name'),[
                                        'prompt'=>'Select Dept for TA',
                                        'onchange'=>'$.post(
                                            "/instructor/instlist",
                                            {
                                                deptId:$(this).val()
                                            },
                                            function(data){
                                                $("#courseoffering2detail-coff2det_ta_relid").html(data);
                                            })',
                                    ])->label('Filter TA by Dept.') ?>
                                     <?=  $model->coff2det_TA_relId ? 
                                        $form->field($model, 'coff2det_TA_relId')->dropDownList(ArrayHelper::map(\backend\models\Instructor::find()->all(),'id',function($model){ return $model->getFullName(); }),['prompt'=>'Select TA']) : 
                                        $form->field($model, 'coff2det_TA_relId')->dropDownList([],['prompt'=>'Select TA']) ?>
                                    <?php
                                }
                            ?>
                        </div>
                  </div>
              </div>
            </div>
        </div><br>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <?= Html::submitButton($model->isNewRecord ? Yii::t('decirs', 'Save and Continue') : Yii::t('decirs', 'Update'), ['class' => $model->isNewRecord ? 'btn btn-success' : 'btn btn-primary btn-sm']) ?>
                </div>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<?php

$this->registerJs(
    "
     $('form#CourseOffering2Detail').on('beforeSubmit',function(e){
            e.preventDefault();
            var \$form = $(this);

            // console.log(\$form.attr('action'));

            $.post(
                \$form.attr('action'),
                \$form.serialize(),
                function(data){
                result = JSON.parse(data);
                if(result['ok'] == 1){
                    \$form.trigger('reset');
                    // console.log(result['message']);
                    // alert(result['message']);
                    //$('#modal').modal('hide');
                    loadCourses();
                    }else{
                       alert(result['message']);
                    }
                }
            );
            return false;
        });

    ",
    $this::POS_READY
);

?>
