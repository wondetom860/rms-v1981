<?php

// use backend\models\CourseOffering2Detail;
use yii\helpers\Html;
use yii\widgets\DetailView;
use yii\bootstrap\Modal;
/** @var yii\web\View $this */
/* @var $model frontend\modules\Reassessment\models\ReassessmentHistory */

$this->title = $model->getRasHeader();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Reassessment Requests'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="reassessment-history-view">

    <h3><?= Yii::t('rms', 'Course Reassessment') . ' : <i>' . Html::encode($this->title) . '</i>' ?>

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
        <p class="pull-right">
            <?= Html::button(Yii::t('rms', 'Print Request Form'), ['class' => 'btn btn-warning btn-xs', 'onclick' => "printRequestForm({$model->id});return false;"]) ?>
            <?= $model->ras_report_scan ? Html::button(Yii::t('rms', 'Print Report'), ['class' => 'btn btn-success btn-xs', 'onclick' => "printRequestReport({$model->id});return false;"]) : '<small>Report not ready for printing...</small>' ?>
        </p>

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
            // [
            //     'attribute' => 'ras_srd2_relId',
            //     'value' => function($model){
            //         return $model->rasStdRel->getFullName();
            //     }
            // ],

            [
                'attribute' => 'ras_course_relId',
                'value' => function ($model) {
                    return $model->rasCourseRel->getCourseCredits().': '.$model->rasCourseRel->course_title;
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
                    // getContinuousAssessmentEntries
                    $assesmentEntries = $model->rasSrd2Rel->getContinuousAssessmentEntries();
                    if($assesmentEntries){
                        return $assesmentEntries[0]->caCreatedBy->getFullName();
                    }
                    // $coff2s = $model->rasGroupRel
                    //     ->getCourseOfferings()
                    //     ->innerJoin('course_offering2_detail', 'course_offering2_detail.coff2det_coff2_relId = course_offering2.id')
                    //     ->andWhere([
                    //         'coff2det_course_relId' => $model->ras_course_relId,
                    //         'coff2_group_relId' => $model->ras_group_relId,
                    //         'course_offering2.id' => $model->rasSrd2Rel->srd2_coff_relId,
                    //         ])
                    //     ->with('courseOffering2Details')
                    //     ->all();
                    // // var_dump($coff2s[0]['courseOffering2Details'][0]->coff2detInstRel->getFullName());exit;
                    // return $coff2s ? $coff2s[0]['courseOffering2Details'][0]->coff2detInstRel->getFullName() : '-';
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
                    return $this->render('__reports/reassessor_mark', ['model' => $model]);
                }
            ],
            [
                'attribute' => 'Reviewing Committee',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->render('__reports/reviewing_committee', ['model' => $model, 'organize' => 1]);
                }
            ],

            [
                'attribute' => 'ras_report_scan',
                'format' => 'raw',
                'value' => function ($model) {

                    return isset($model->ras_report_scan) ? Html::button(
                        Yii::t('hrms', 'View File'),
                        [
                            'class' => 'btn btn-xs pull-right btn-primary',
                            'onclick' => 'readReportScan(' . $model->id . ')',
                        ]
                    ) :   Html::button(
                        Yii::t('hrms', 'Upload'),
                        [
                            'class' => 'btn btn-xs pull-right btn-warning',
                            'onclick' => 'uploadReportScan(' . $model->id . ')',
                        ]
                    );
                    // return Html::a($model->inventoryRel->getInventoryHeader(), ['/INVSYS/invsys-inventory/view', 'id' => $model->inventory_relId]);
                }
            ],
        ],
    ]) ?>

</div>

<?php
Modal::begin([
    'header' => '<h4></h4>',
    'id' => 'modal',
    'size' => 'modal-lg',
]);
echo "<div id='modalContent'>Loading form...</div>";
Modal::end();
?>

<script>
    const readReportScan = (ras_id) => {
        $('#modal').find('.modal-header').html('attached report file');
        $('#modalContent').html("Loading file...");
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/Reassessment/reassessment-history/read-scan-file', {
            ras_id: ras_id
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
    const submitRaMark = (ras_id) => {
        $('#modal').find('.modal-header').html('Submitting reassessment mark');
        $('#modalContent').html("Loading form...");
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/Reassessment/reassessment-history/submit-ra-mark', {
            ras_id: ras_id
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
    const printRaForm = (ras_id) => {
        myWindow = window.open(
            '/Reassessment/reassessment-history/request-form-print?gadget=Typical&ras_id=' + ras_id,
            'title=Request application form',
            'height=800,width=900');
    }
    const printRequestReport = (ras_id) => {
        $('#modal').find('.modal-header').html('Generate request report');
        $('#modalContent').html("Loading application report");
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/Reassessment/reassessment-history/request-report', {
            ras_id: ras_id
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
    const printRequestForm = (ras_id) => {
        $('#modal').find('.modal-header').html('Generate request form');
        $('#modalContent').html("Loading application form");
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/Reassessment/reassessment-history/request-form', {
            ras_id: ras_id
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
    const assignCommitteee = (ras_id) => {
        $('#modal').find('.modal-header').html('Organize reviewing committee');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/Reassessment/reassessment-committee/form-committee', {
            ras_id: ras_id
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
    const uploadReportScan = (ras_id) => {
        $('#modal').find('.modal-header').html('Course Reassessment Report [JPG,PNG file types only]');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/Reassessment/reassessment-history/upload-image', {
            ras_id: ras_id
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
</script>