<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model frontend\modules\GC\models\GcEntry */

$this->title = $model->header_title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Gc Entries'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="gc-entry-view col-md-5">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?php //Html::a(Yii::t('rms', 'Update'), ['update', 'id' => $model->id], ['class' => 'btn btn-primary btn-xs']) 
            ?>
            <?= Html::a(Yii::t('rms', 'Delete'), ['delete', 'id' => $model->id], [
                'class' => 'btn btn-danger btn-xs',
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
                'attribute' => 'acad_year_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->acadYearRel->getAcHeader();
                }
            ],
            [
                'attribute' => 'program_relId',
                'value' => function ($model) {
                    return $model->admissionRel->admission_name . ' ' . $model->programRel->program_name;
                }
            ],
            [
                'attribute' => 'dept_relId',
                'value' => function ($model) {
                    return $model->deptRel->dept_name;
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
                'format' => 'raw',
                'value' => function ($model) {
                    $btnUpdate = "<button class='btn btn-xs btn-warning pull-right' onclick='updateDate({$model->id});return false;'>Update</button>";
                    return date('dMY', $model->senate_approve_date) . $btnUpdate;
                }
            ],
            [
                'attribute' => 'created_at',
                'value' => function ($model) {
                    return date('dMY', $model->created_at);
                }
            ],

            // 'updated_at',
            // 'deleted_at',
            // 'deleted_by',
            // 'created_by',
            // 'updated_by',
            // 'data:ntext',
        ],
    ]) ?>

</div>
<div class="col-md-7">
    <h4>Students <button title='import students from this program' class="btn btn-xs btn-primary pull-right" onclick='importStudentListfromAcp(<?= $model->id ?>);return false;'>Import Students</button></h4>
    <div id='std_ls_container'>
        <?= $this->render('__report/student_list', ['model' => $model]) ?>
    </div>
</div>

<script>
    const updateDate = function(gc_id) {
        $.ajax({
            url: '/GC/gc-entry/update?id=' + gc_id,
            type: 'POST',
            data: {
                id: gc_id
            },
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
    const changeApproveStatusBatch = function(gc_id, option) {
        if (confirm('Are you sure to change status?')) {
            $.post('/GC/gc-student/approve-gc-students-batch', {
                gc_id: gc_id,
                option: option
            }).done((resp) => {
                if (resp == 1) {
                    window.location.href = window.location;
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
                    window.location.href = window.location;
                } else {
                    alert(resp);
                }
            });
        }
    }

    const DeleteFromList = function(gc_id) {
        if (confirm('Are you sure to delete?')) {
            $.post('/GC/gc-student/delete?id=' + gc_id, {}).done((resp) => {
                if (resp == 1) {
                    window.location.href = window.location;
                } else {
                    alert(resp);
                }
            });
        }
    }
    __gc_list = [];
    const submitGcList = function(gc_entry_id) {
        $('#modalContent').html('Submitting...');
        $.post('/GC/gc-entry/create-gc-students', {
            gc_entry_id: gc_entry_id,
            gc_list: JSON.stringify(__gc_list)
        }).done((resp) => {
            if (resp == 1) {
                window.location.href = window.location;
            } else {
                alert(resp);
            }
        });
    }
    const addToGcList = function(stdId) {
        r = 0;
        if (jQuery.inArray(stdId, __gc_list) !== -1) {
            // if item is already selected, remove it because user deselected it.
            __gc_list.splice($.inArray(stdId, __gc_list), 1);
            r = 0;
        } else { //going to add the item to the list.
            __gc_list.push(stdId);
            r = 1;
        }
        // console.log(__gc_list.length);
        $("#selected_count").text(__gc_list.length);
    }
    const importStudentListfromAcp = function(entry_id) {
        __gc_list = [];
        $.ajax({
            url: '<?= \yii\helpers\Url::to(['gc-entry/import-students-from-active-program']) ?>',
            type: 'POST',
            data: {
                id: entry_id
            },
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