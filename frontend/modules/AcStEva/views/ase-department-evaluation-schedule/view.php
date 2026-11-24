<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model frontend\modules\AcStEva\models\AseDepartmentEvaluationSchedule */

$this->title = $model->headerRel->evaluation_header . " [{$model->criteriaBundleRel->code}]";
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'AcStEva'), 'url' => ['/AcStEva']];
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Department Evaluation Schedules'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
$model->startdate = date("d/m/Y", $model->start_date);
$model->enddate = date("d/m/Y", $model->end_date);
?>
<div class="ase-department-evaluation-schedule-view">

    <h3>
        <?= Html::encode($this->title) ?>
    </h3>
    <?php if (Yii::$app->user->can('DepartmentHead')): ?>
        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']) ?>
            <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-xs',
                'data' => [
                    'confirm' => Yii::t('rms', 'Are you sure you want to delete this item?'),
                    'method' => 'post',
                ],
            ]) ?>
        </p>
    <?php endif ?>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'header_relId',
                'value' => function ($model) {
                    return $model->headerRel->evaluation_header;
                }
            ],
            [
                'attribute' => 'dept_relId',
                'value' => function ($model) {
                    return $model->deptRel ? $model->deptRel->dept_name : '-';
                }
            ],
            [
                'attribute' => 'criteria_bundle_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    $criteriaCount = count(json_decode($model->criteriaBundleRel->criteria_IDS));
                    $count_link = "<a href='#' onclick='showEvaluationCriteriaDetail({$model->criteria_bundle_relId})'>{$criteriaCount}</a>";
                    return $model->criteriaBundleRel->code . " [{$model->criteriaBundleRel->evaluator_role} >> {$model->criteriaBundleRel->candidate_staff_role}] &nbsp;&nbsp;&nbsp;&nbsp;{$count_link} entries";
                }
            ],
            'finalized',
            [
                'attribute' => 'Schedule',
                'format' => 'raw',
                'value' => function ($model) {
                    $status = "<span class='pull-right bg-warning' style='font-size: 10pt; font-style: italic;'>Status: {$model->getStatus()}</span>";
                    $extendBtn = '';
                    if($model->headerRel->status == \frontend\modules\AcStEva\models\AseSemesterEvaluation::STATUS_ACTIVE && $model->status == \frontend\modules\AcStEva\models\AseDepartmentEvaluationSchedule::STATUS_CLOSED) {
                        $extendBtn = Html::a('Extend Schedule', ['extend-schedule', 'id' => $model->id], ['class' => 'btn btn-success btn-xs pull-right']);
                    }
                    return $model->getScheduleInterval() . "{$status}{$extendBtn}";
                }
            ],
            [
                'attribute' => 'Evaluation Per-Course',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->criteriaBundleRel->evaluate_per_course ? "YES" : "NO";
                }
            ],
            [
                'attribute' => 'Evaluation Entries',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->render('../ase-evaluation-control-setting/__report/evaluation-control-entries', ['model' => $model]);
                }
            ],
            // 'created_by',
            // 'created_at',
            // 'updated_at',
            // 'updated_by',
            // 'data:ntext',
        ],
    ]) ?>

</div>

<script type="text/javascript">
    __candidate_staff_id = null;
    __evaluators = [];
    __deptPlanId = '';
    __bundle_id = null;
    __falulty_model_id = null

    const CreateControlSetting = () => {
        if (__evaluators.length && __candidate_staff_id) {
            console.log(__candidate_staff_id, __evaluators, __deptPlanId, __bundle_id);
            if (confirm("Are you sure to save this setup?")) {
                $.post('/AcStEva/ase-evaluation-control-setting/create2', {
                        'deptPlanId': __deptPlanId,
                        'candidate_staff_relId': __candidate_staff_id,
                        'evalautors': JSON.stringify(__evaluators),
                        'facultyModelId': __falulty_model_id,
                        'bundleId': __bundle_id,
                    })
                    .done((resp) => {
                        if (resp == 1) {
                            generateControlSetting(__deptPlanId, __bundle_id);
                        } else {
                            alert(resp);
                        }
                    });
            }
        } else {
            alert('Insufficient input, please complete mandatory inputs.');
        }
    }
    const addToEvaluatorList = (userid, deptPlanId, facuId, facultyModelId) => {
        console.log(__candidate_staff_id, __evaluators, __deptPlanId, __falulty_model_id, __bundle_id);
        addToList(userid, deptPlanId, facuId, facultyModelId);
    }

    const addAllToEvaluatorList = (deptPlanId, facuId, facultyModelId) => {
        $(".evaluators_" + facuId + "_" + facultyModelId).trigger('change');
        console.log(__candidate_staff_id, __evaluators, __deptPlanId, __falulty_model_id, __bundle_id);
    }

    const addToList = (userid, deptPlanId, facuId, facultyModelId) => { //evaluator_id,plan_id,faculty_user_id
        r = 0;
        if (jQuery.inArray(userid, __evaluators) !== -1) {
            // if item is already selected, remove it because user deselected it.
            __evaluators.splice($.inArray(userid, __evaluators), 1);
            r = 0;
        } else { //going to add the item to the list.
            __evaluators.push(userid);
            r = 1;
        }
        // $('#asesemesterevaluation-criteria_bundle_ids').val(JSON.stringify(__evaluators));
        $('#evalist__' + userid + '_' + deptPlanId + '_' + facuId + "_" + facultyModelId).prop({
            'checked': (r == 1)
        });
        $("#elas_count_" + facuId + "_" + deptPlanId + "_" + facultyModelId).text(__evaluators.length);
    }

    window.getCandidateEvaluators = (facuId, deptPlanId, faculty_inst_user_id) => {
        __evaluators = [];
        __candidate_staff_id = faculty_inst_user_id;
        __deptPlanId = deptPlanId;
        __falulty_model_id = facuId;
        // __evaluators
        $("#evalautors_id_" + facuId).text("Loading eligible evaluators...");
        // $("#evalautors_id_"+facuId).parent().toggle('display');
        // console.log($("#evalautors_id_"+facuId).parent().css('display') == "none");
        $('.candidate_staff_container').css('display', 'none');
        if ($("#evalautors_id_" + facuId).parent().css('display') == "none") {
            $("#evalautors_id_" + facuId).parent().toggle('display')
        } else {
            __evaluators = [];
            $("#evalautors_id_" + facuId).parent().html('').toggle('display');
            return;
        }

        try {
            _evaluators_ = $('#facu__' + facuId).data('evaluators');
        } catch (erx) {
            console.log(erx);
        }
        $.get('/AcStEva/ase-evaluation-control-setting/get-evaluators-for-candidate', {
            facuId: facuId,
            deptPlanId: deptPlanId
        }).done((resp) => {
            // evaluators: 
            $("#evalautors_id_" + facuId).html(resp);
            if (_evaluators_.length) {
                $.each(_evaluators_, (index, evaluator) => {
                    // console.log('Adding to List : '+evaluator);
                    // $("#evalautors_id_"+facuId).parent().toggle('display');
                    addToList(evaluator, deptPlanId, __candidate_staff_id, __falulty_model_id);
                });
            }
        });
    }

    const displayEvaluators = (controlId) => {
        // __candidate_staff_id = '';
        // __evaluators = [];
        // __bundle_id = bundleId;
        $('#modal').find('.modal-header').html('Evaluators List:');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/AcStEva/ase-evaluation-control-setting/show-evaluators', {
            id: controlId
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }

    window.toggleDisplay = (id) => {
        $('#' + id).toggle('display');
    }

    const redirectPage2 = () => {
        window.location.href = "/AcStEva/ase-evaluation-control-setting/";
    }

    window.showEvaluationCriteriaDetail = (bundleId) => {
        __candidate_staff_id = '';
        __evaluators = [];
        __bundle_id = bundleId;
        console.log(__bundle_id);
        $('#modal').find('.modal-header').html('Evaluation Criteria Deatils');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Loading form...');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/AcStEva/ase-criteria-bundle/show-criteria-detail', {
            bundleId: bundleId
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }

    const generateControlSetting = (deptPlanId, bundleId) => {
        __bundle_id = bundleId;
        // console.log(deptPlanId, bundleId);
        $('#modal').find('.modal-header').html('Evaluation Control Setting Entries: ');
        $('#modalContent').css({
            'text-align': 'left',
            'margin-top': '0px'
        });
        $('#modalContent').html('Generating for you....');
        $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
        $('#modal').modal('show');
        $.get('/AcStEva/ase-evaluation-control-setting/generate-control-entries', {
            deptPlanId: deptPlanId,
            bundleId: bundleId
        }).done(function(data) {
            $('#modalContent').html(data);
        });
    }
</script>