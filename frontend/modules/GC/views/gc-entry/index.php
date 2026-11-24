<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\GC\models\GcEntrySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'GC Years');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gc-entry-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Create Entry'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
            <button title="Import from GC academic programs" class="btn btn-xs btn-primary" onclick="importFromGcAcademicProgram();return false;">Import from ACADEMIC PROGRAMS</button>
        </p>

    </h3>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'acad_year_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->acadYearRel->getAcHeader();
                }
            ],
            [
                'attribute' => 'admission_relId',
                'value' => function ($model) {
                    return $model->admissionRel->admission_name;
                }
            ],
            [
                'attribute' => 'program_relId',
                'value' => function ($model) {
                    return $model->programRel->program_name;
                }
            ],
            [
                'attribute' => 'dept_relId',
                'value' => function ($model) {
                    return $model->deptRel->dept_code;
                }
            ],

            [
                'attribute' => 'spec_relId',
                'value' => function ($model) {
                    return $model->specRel ? $model->specRel->spec_name : '-';
                }
            ],
            [
                'attribute' => 'senate_approve_date',
                'value' => function ($model) {
                    return date('dMY', $model->senate_approve_date);
                }
            ],
            [
                'attribute' => 'Students',
                'format' => 'raw',
                'value' => function ($model) {
                    $approved = $model->getGcStudents()->andWhere(['reg_approved' => 1])->count();
                    $bcbt = count($model->gcStudents) . "/" . $approved;
                    return Html::a("{$bcbt}", '#', ['onclick' => "showStudentList($model->id);return false;"]);
                }
            ],
            // 'header_title',
            // 'created_at',
            //'updated_at',
            //'deleted_at',
            //'deleted_by',
            //'created_by',
            //'updated_by',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>

<script>
    const changeApproveStatusBatch = function(gc_id, option) {
        if (confirm('Are you sure to change status?')) {
            $.post('/GC/gc-student/approve-gc-students-batch', {
                gc_id: gc_id,
                option: option
            }).done((resp) => {
                if (resp == 1) {
                    showStudentList(gc_id);
                } else {
                    alert(resp);
                }
            });
        }
    }
    const changeRegApprovalStatus = function(gc_id, gs_id, option) {
        if (confirm('Are you sure to change status?')) {
            $.post('/GC/gc-student/approve-gc-students', {
                gc_id: gs_id,
                option: option
            }).done((resp) => {
                if (resp == 1) {
                    showStudentList(gc_id);
                } else {
                    alert(resp);
                }
            });
        }
    }
    const importfromacp = function(acp_id) {
        $.post('/GC/gc-entry/import-from-acp', {
            acp_id: acp_id
        }).done((resp) => {
            if (resp == 1) {
                window.location.href = window.location;
            } else {
                alert(resp);
            }
        });
    }
    const showStudentList = function(gc_id) {
        $.post('/GC/gc-entry/get-students-list', {
            gc_id: gc_id
        }).done((data) => {
            $('#modal').find('.modal-header').html('Graduate Students from Active Program');
            $('#modalContent').css({
                'text-align': 'left',
                'margin-top': '0px'
            });
            $('#modalContent').html('Loading form...');
            $('#modelContent').addClass('table-responsive');
            $('#modalContent').css('overflow', 'auto');
            $('#modalContent').css('height', '500px');
            $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
            $('#modal').modal('show');
            $('#modalContent').html(data);
        });
    }
    const importFromGcAcademicProgram = function() {
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['gc-entry/import-from-active-programs']) ?>',
            type: 'GET',
            dataType: 'html',
            success: function(data) {
                $('#modal').find('.modal-header').html('Graduate Students from Active Program');
                $('#modalContent').css({
                    'text-align': 'left',
                    'margin-top': '0px'
                });
                $('#modalContent').html('Loading form...');
                $('#modelContent').addClass('table-responsive');
                $('#modalContent').css('overflow', 'auto');
                $('#modalContent').css('height', '500px');
                $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
                $('#modal').modal('show');
                $('#modalContent').html(data);
            }
        });
    }
</script>