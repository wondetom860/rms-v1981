<?php

use yii\helpers\Html;
use yii\widgets\ActiveForm;

/** @var yii\web\View $this */
/* @var $model frontend\modules\ExamSchedule\models\ExExaminees */
/* @var $form yii\widgets\ActiveForm */
$inst = $model->offeringDetailRel->coff2detInstRel;
?>

<div class="ex-examinees-form">

    <?php $form = ActiveForm::begin(['action' => ['#']]); ?>

    <div class="hidden">
        <?= $form->field($model, 'exam_slot_relId')->textInput() ?>

        <?= $form->field($model, 'section_relId')->textInput() ?>

        <?= $form->field($model, 'offering_detail_relId')->textInput() ?>
    </div>

    <div class="row bg-info">
        <div class="col-md-7">
            <p>Exam Schedule Entry: <b><u><?= $model->examSlotRel->dateTimeSlotRel->getHeaderText() ?></u></b></p>
            <p>Exam Room: <b><u><?= $model->examSlotRel->classRoomRel->room ?></u></b></p>
            <p>Course: <b><u><?= $model->offeringDetailRel->coff2detCourseRel->getCourseCredits() . " : " . $model->offeringDetailRel->coff2detCourseRel->course_title ?></u></b></p>
        </div>
        <div class="col-md-5">
            <p>Section: <b><u><?= $model->sectionRel->getGsDisp() ?></u></b></p>
            <p>Instructor: <b><u><?= $inst ? $inst->getFullName() : ' - ' ?></u></b></p>
            <p>Phone Number: <b><u><?= $inst ? $inst->instUserRel->phone_mobile : ' - ' ?></u></b></p>
        </div>
    </div>
    <div class="row" style="max-height: 400px; overflow-y: auto;">
        <div class="col-md-12">
            <?= $this->render('__report/course-enrollments', ['model' => $model]) ?>
        </div>
    </div>

    <div class="row bg-warning">
        <div class="col-md-10">
            Selected: <span id="selecteds_001"></span>
        </div>
        <div class="col-md-2">
            <div class="form-group">
                <?= Html::submitButton(Yii::t('rms', 'Save'), ['class' => 'btn btn-success btn-xs pull-right', 'onclick' => "SaveAssignment(); return false;"]) ?>
            </div>
        </div>
    </div>

    <?php ActiveForm::end(); ?>

</div>

<script>
    __selectedsArr = [];
    window.SaveAssignment = () => {
        if (__selectedsArr.length > 0) {
            let data = {
                'exam_slot_relid': document.getElementById('exexaminees-exam_slot_relid').value,
                'section_relid': document.getElementById('exexaminees-section_relid').value,
                'offering_detail_relid': document.getElementById('exexaminees-offering_detail_relid').value,
                'selecteds': JSON.stringify(__selectedsArr)
            };
            if (confirm('You want to save assignments')) {
                $.post('<?= Yii::$app->urlManager->createUrl(['/ExamSchedule/ex-examinees/save-assignment']) ?>', data, (response) => {
                    if (response == 1) {
                        alert('Assignment saved successfully.');
                        window.location.reload();
                    } else {
                        console.log(response);
                        alert('Error saving assignment.'+response);
                    }
                });
                // window.location.reload();
            }
        } else {
            alert('No student selected.');
        }
        return false;
    }
    window.assignStudent = (stdid, examineesid) => {
        let chk = document.getElementById(`chkbx_001_${stdid}`);
        let selecteds = document.getElementById('selecteds_001');
        // let __selectedsArr = selecteds.innerText.split(',');
        if (chk.checked) {
            __selectedsArr.push(stdid);
        } else {
            __selectedsArr = __selectedsArr.filter((item) => item !== stdid);
        }
        selecteds.innerText = __selectedsArr.length;
        // console.log(__selectedsArr);
    }
</script>