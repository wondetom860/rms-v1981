<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */

/** @var frontend\modules\TRANSTUD\models\TransferStudent $model*/

$this->title = $model->stdRel->getFullName();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Transfer Students'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="transfer-student-view">

    <h3><?= Html::encode($this->title) ?>

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

    </h3>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [

            [
                'attribute' => 'std_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    $std = $model->stdRel;
                    $htt = "{$std->std_regId} : {$std->getFullName()}";
                    return Html::a($htt, ['/student/view', 'id' => $model->std_relId], []);
                }
            ],
            'transfered_from',
            'year_admitted',
            [
                'attribute' => 'year_transfered',
                'value' => function ($model) {
                    return "{$model->yearTransfered->getAcHeader()}";
                }
            ],
            [
                'attribute' => 'class_level_joined',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->classLevelJoined ? $model->classLevelJoined->getClslvlDisp() : Yii::t('rms', 'Unknown');
                }
            ],
            'sgpa',
            'cgpa',
            [
                'attribute' => 'approved',
                'format' => 'raw',
                'value' => function ($model) {
                    if ($model->approved == 1) {
                        return Html::tag('span', Yii::t('rms', 'Yes'), ['class' => 'label label-success']);
                    } elseif ($model->approved == 0) {
                        return Html::tag('span', Yii::t('rms', 'No'), ['class' => 'label label-danger']);
                    }
                    // return $model->approved ? Yii::t('rms', 'Yes') : Yii::t('rms', 'No');
                }
            ],
            [
                'attribute' => 'exempted_course_ids',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->render('__report/_exempted_courses', [
                        'model' => $model,
                    ]);
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

<script>
    const changeApproveStatus = (tsId, option) => {
        const url = `<?= \yii\helpers\Url::to(['/TRANSTUD/transfer-student/approve-ts-record', 'id' => '']) ?>${tsId}&option=${option}`;
        if (confirm('Are you sure you want to change the approval status?')) {
            $.get(url, function(data) {
                if (data.success) {
                    alert('Approval status changed successfully.');
                    location.reload();
                } else {
                    // console.error(data.errors);
                    alert('Failed to change approval status.');
                }
            });
        }
    }
    const removeEquivCourse = (eCourseId, EqCourseId) => {
        const url = `<?= \yii\helpers\Url::to(['/TRANSTUD/exempted-course/remove-equiv-course', 'eCourseId' => '']) ?>${eCourseId}&eqCourseId=${EqCourseId}`;
        if (confirm('Are you sure you want to remove this equivalent course?')) {
            $.get(url, function(data) {
                if (data.success) {
                    alert('Equivalent course removed successfully.');
                    location.reload();
                } else {
                    // console.error(data.errors);
                    alert('Failed to remove equivalent course.');
                }
            });
        }
    }
    const addEquivalentCourse = (courseId) => {
        const url = `<?= \yii\helpers\Url::to(['/TRANSTUD/exempted-course/add-equivalent', 'tCourseId' => '']) ?>${courseId}`;
        $('#modal').find('.modal-header').html('Add Equivalent Course');
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
        $('#modalContent').html('Loading form...');
        $.get(url, function(data) {
            $('#modalContent').html(data);
        });
    }
    const addExemptedCourses = (tsId) => {
        const url = `<?= \yii\helpers\Url::to(['/TRANSTUD/exempted-course/create']) ?>?tsId=${tsId}`;
        $('#modal').find('.modal-header').html('Transfer Students - Add Exempted Course');
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
        $('#modalContent').html('Loading form...');
        $.get(url, function(data) {
            $('#modalContent').html(data);
        });
    };
</script>