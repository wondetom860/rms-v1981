<?php

// use yii\helpers\Html;
use yii\widgets\DetailView;
?>
<h3 style="text-align: center;">Reassessment/Regrading Application Form
    <?= !isset($print) ? "<button onclick='printRaForm({$model->id});return false;' class='btn btn-link pull-right'>Print</button>" : '' ?>
</h3>
<?= DetailView::widget([
    'model' => $model,
    'attributes' => [
        [
            'attribute' => 'ras_std_relId',
            'value' => function ($model) {
                return $model->rasStdRel->getFullName();
            }
        ],
        [
            'attribute' => 'Reg. ID',
            'value' => function ($model) {
                return $model->rasStdRel->std_regId;
            }
        ],

        [
            'attribute' => 'ras_course_relId',
            'value' => function ($model) {
                return $model->rasCourseRel->getCourseCredits() . " : " . $model->rasCourseRel->course_title;
            }
        ],

        [
            'attribute' => 'ras_acadYear_relId',
            'value' => function ($model) {
                return $model->rasAcadYearRel->sch_year_desc . "-" . $model->rasSemRel->semester_name;
            }
        ],

        [
            'attribute' => 'ras_group_relId',
            'format' => 'raw',
            'value' => function ($model) {
                return $model->rasGroupRel->getGsDisp();
            }
        ],
        [
            'attribute' => 'Course Instructor',
            'value' => function ($model) {
                $coff2Det = $model->rasSrd2Rel->getCourseInstructor();
                    if($coff2Det){
                        return $coff2Det->coff2detInstRel->getFullName();
                    }
                $coff2s = $model->rasGroupRel
                    ->getCourseOfferings()
                    ->innerJoin('course_offering2_detail', 'course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
                    ->where(['coff2det_course_relId' => $model->ras_course_relId])
                    ->with('courseOffering2Details')
                    ->all();
                // var_dump($coff2s[0]['courseOffering2Details'][0]->coff2detInstRel->getFullName());exit;
                return $coff2s ? $coff2s[0]['courseOffering2Details'][0]->coff2detInstRel->getFullName() : '-';
            }
        ],
        [
            'attribute' => 'ras_registrarApprove_status',
            'format' => 'raw',
            'value' => function ($model) {
                return $model->ras_registrarApprove_status ? "Approved on " . date_format(date_create($model->ras_registrarApprove_at), 'dMY') : '<span class="badge label-warning">Not Yet</span>';
            }
        ],

        // 'ras_status',
        // 'ras_app_date',
        // 'ras_approved_by',
        // [
        //     'attribute' => 'ras_bef_grade_relId',
        //     'value' => function ($model) {
        //         return $model->ras_bef_grade_relId ? $model->rasBefGradeRel->gs_ltr_grade : '-';
        //     }
        // ],
        // [
        //     'attribute' => 'ras_aft_grade_relId',
        //     'value' => function ($model) {
        //         return $model->ras_aft_grade_relId ? $model->rasAftGradeRel->gs_ltr_grade : '-';
        //     }
        // ],
        [
            'attribute' => 'ras_contAssCatag_relId',
            'format' => 'raw',
            'value' => function ($model) {
                return $this->render('../__reports/reassessor_mark', ['model' => $model, 'print' => 1]);
            }
        ],
        [
            'attribute' => 'Reviewing Committee',
            'format' => 'raw',
            'value' => function ($model) {
                return $this->render('../__reports/reviewing_committee', ['model' => $model, 'print' => 1]);
            }
        ],
    ],
]) ?>

<br>
<br>
<br>
<br>
<br>