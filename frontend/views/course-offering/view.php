<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** 
 * @var yii\web\View $this 
* @var backend\models\CourseOfferingTemp $model 
*/

if ($model->cot_acProg_relId) {
    $header = $model->cotAcProgRel->getClassLevelHeader();
} else {
    $mn = $model->linkAcProg();
    if (is_bool($mn) && $mn) {
        $header = $model->cotAcProgRel->getClassLevelHeader();
    } else {
        $header = $mn;
    }
}
$this->title = "Course Offering";
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Course Offerings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="course-offering-temp-view">

    <h3><?= Html::encode($this->title) . ": " . $header ?>
        <p class="hidden">
            <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary']) ?>
            <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger',
                'data' => [
                    'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    </h3>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'cot_acadYear_relId',
                'value' => function ($model) {
                    return $model->cotAcadYearRel->sch_year_desc . "-" . $model->cotSemRel->semester_name;
                },

            ],

            [
                'attribute' => 'cot_prog_relId',
                'value' => function ($model) {
                    return $model->cotProgRel->program_name . ' ' . $model->cotAdmRel->admission_name;
                },

            ],
            [
                'attribute' => 'cot_dept_relId',
                'value' => function ($model) {
                    return $model->cotDeptRel->dept_name;
                },

            ],
            [
                'attribute' => 'cot_clslvel_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->cotClslvelRel->getClslvlDisp();
                },

            ],
            [
                'attribute' => 'cot_spec_relId',
                'value' => function ($model) {
                    return $model->cot_spec_relId ? $model->cotSpecRel->spec_name : "-";
                }
            ],
            [
                'attribute' => 'cot_cocat_IDS',
                'format' => 'raw',
                'value' => function ($model) {
                    // return count(json_decode($model->cot_cocat_IDS, true));
                    return $this->render('__reports/courses-offered', ['model' => $model]);
                }
            ],
            // 'cot_created_at',
            // 'cot_created_by',
            // 'cot_updated_at',
            // 'cot_updated_by',
            // 'cot_locked',
            // 'cot_registrarApproved',
            // 'cot_registrarApproved_at',
            // 'cot_registrarApproved_by',
            [
                'attribute' => 'cot_created_at',
                'format' => 'raw',
                'value' => function ($model) {
                    return date_format(date_create($model->cot_created_at), 'dMY');
                }
            ],
            [
                'attribute' => 'cot_acProg_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->cot_acProg_relId) {
                        return "<a href='/AcademicCalendar/ac-active-programs/view?id={$model->cot_acProg_relId}'>{$model->cotAcProgRel->getClassLevelHeader()}</a>";
                    } else {
                        return Html::a('Create Academic Program', '', ['onclick' => "createAcp({$model->id});return false;", 'class' => 'btn btn-link pull-right']);
                    }
                }
            ],
        ],
    ]) ?>
    <script>
        const createAcp = (cotId) => {
            // alert("creating...." + cotId);
            if (confirm('Are you sure to create academic program for this offering?')) {
                $.post('/course-offering/create-acp', {
                    cot_id: cotId
                }).done((resp) => {
                    if (resp == 1) {
                        window.location.href = window.location;
                    } else {
                        alert(resp);
                    }
                });
            }
        }
    </script>
</div>