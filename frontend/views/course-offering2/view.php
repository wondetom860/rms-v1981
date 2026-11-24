<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/** 
 * @var yii\web\View  $this 
 * @var $model backend\models\CourseOffering2
 */

$this->title = $model->getCourseOfferingHeader();
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Course Offerings'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-offering2-view">

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
                'attribute' => 'coff2_acadYear_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->getOfferingAcadeYearInfo();
                },
            ],

            [
                'attribute' => 'admission',
                'value' => function ($model) {
                    return $model->coff2GroupRel->gsAdmRel->admission_name;
                },
            ],

            [
                'attribute' => 'program',
                'value' => function ($model) {
                    return $model->coff2GroupRel->gsProgRel->program_name;
                },
            ],
            [
                'attribute' => 'department',
                'value' => function ($model) {
                    return $model->coff2GroupRel->gsDeptRel->dept_name;
                },
            ],
            [
                'attribute' => 'clslvl',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->coff2GroupRel->gsClslvlRel->getClslvlDisp();
                },
            ],

            [
                'attribute' => 'coff2_group_relId',
                'value' => function ($model) {
                    return $model->coff2GroupRel->gsGroupRel->group_name;
                },
            ],
            [
                'attribute' => 'Courses',
                'format' => 'raw',
                'value' => function ($model) {
                    return $this->render('__reports/courses-offered', ['model' => $model]);
                },
            ],
        ],
    ]) ?>
    <script>
        function addOffering(id) {
            $('#modal').find('.modal-header').html('Course Offering (For Transfer courses)');
            $('#modalContent').css('text-align', 'left');
            $('#modalContent').html('Loading data...');
            $('#modalContent').addClass('table-reponsive');
            $('#modal').find('.modal-dialog').removeClass('modal-*').addClass('modal-lg');
            $('#modal').modal('show');
            $.post('/course-offering2-detail/add-offering', {
                coff2Id: id
            }).done(function(data) {
                $('#modalContent').html(data);
            }); 
        }
    </script>
</div>