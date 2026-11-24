<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ClassSchedule\models\CsClassScheduleDetail */
/* @var $form yii\widgets\ActiveForm */
?>

<div class="cs-class-schedule-detail-form">

    <?php $form = ActiveForm::begin(); ?>

    <div class="row">
        <div class="col-md-12">
            <div class="container-fluid">
                <div class="col-md-4">
                    Group : <?= $model->gsetRel->gsGroupRel->group_name." - {$model->gsetRel->gsDeptRel->dept_code}" ?><br>
                    Course Offering : <?= $model->coff2detRel->coff2detCourseRel->course_code." - {$model->coff2detRel->coff2detInstRel->getFullName_av()}" ?>
                    <div class="hidden">
                        Class Time : <small id="fsfh_conatiner"></small>
                    </div>
                </div>
                <div class="col-md-8">
                    <div class="hidden">
                        <?= $form->field($model, 'gset_relId')->textInput() ?>

                        <?= $form->field($model, 'class_time_distrib_relId')->textInput() ?>

                        <?= $form->field($model, 'coff2det_relId')->textInput() ?>

                        <?= $form->field($model, 'class_type')->textInput() ?>
                    </div>
                    <div class="row">
                        <div class="col-md-10">
                            <?= $form->field($model, 'room_relId')->dropdownList(ArrayHelper::map(\frontend\modules\ClassSchedule\models\CsAvailableClassRoom::find()->where(['status' => 1])->all(),'id','room'),['prompt' => 'Select Class Room']) ?>
                        </div>
                        <div class="col-md-2"><br>
                            <button class="btn btn-info btn-sm" onclick="getFreeSlot();return false;" title="get free slot for your selection">Get slot</button>
                        </div>
                    </div>
                    <div class="row" style="background-color: #FED;">
                        <div class="col-md-12" id="available_time_slots_container" style="max-height: 300px; overflow: auto;">
                            
                        </div>
                    </div>
                </div>
                
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group pull-right">
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success']) ?>
            </div>
        </div>
        <script type="text/javascript">
            window.setScheduleTime = (keyDistId) => {
                // $(".radio__").prop({'checked'=>false});
                // $("#radio__"+keyDistId).prop({'checked'=>true});

                $('.p__').removeClass('bg-success');
                $('#p__'+keyDistId).addClass('bg-success');

                $("#csclassscheduledetail-class_time_distrib_relid").val(keyDistId);

                $("#fsfh_conatiner").html($('#p__'+keyDistId).text());
                $("#fsfh_conatiner").parent().removeClass('hidden');
            }
            window.getFreeSlot = ()=>{

                gset = $('#csclassscheduledetail-gset_relid').val();
                coffdetId=$('#csclassscheduledetail-coff2det_relid').val();
                room=$('#csclassscheduledetail-room_relid').val();
                type =$('#csclassscheduledetail-class_type').val();
                $("#available_time_slots_container").html('Getting avalialable time slots for your selection...');

                $.post('/ClassSchedule/cs-class-schedule-detail/get-free-slot',{gset:gset,coffdetId:coffdetId,room:room,type:type}).done((response) => {
                    $("#available_time_slots_container").html(response);
                })
            }
        </script>
    </div>

    <?php ActiveForm::end(); ?>

</div>
