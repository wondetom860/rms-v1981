<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/** @var frontend\modules\TRANSTUD\models\ExemptedCourse $model */

$this->title = $model->course_code . ' : ' . $model->course_name;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Exempted Courses'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="exempted-course-view">

    <h3><?= 'Transfer Student >> ' . Html::encode($this->title) ?>

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
                'attribute' => 'ts_relid',
                'format' => 'raw',
                'value' => function ($model) {
                    $ts = $model->tsRel;
                    $htt = "{$ts->stdRel->std_regId} : {$ts->stdRel->getFullName()}";
                    return Html::a($htt, ['/TRANSTUD/transfer-student/view', 'id' => $ts->id], []);
                }
            ],
            'course_code',
            'course_name',
            'course_hours',
            'grade_obtained',
            'grade_point',
            'grade_point_sum',
            [
                'attribute' => 'equivalent_course_ids',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->equivalent_course_ids ? $this->render('__report/equiv-courses', [
                        'model' => $model,
                        'eqCourses' => json_decode($model->equivalent_course_ids, true),
                    ]) : Yii::t('rms', 'No equivalent courses');
                }
            ],
            [
                'attribute' => 'approved',
                'format' => 'raw',
                'value' => function($model){
                    if($model->approved == 1){
                        return Html::tag('span', Yii::t('rms', 'Yes'), ['class' => 'label label-success']);
                    } elseif ($model->approved == 0) {
                        return Html::tag('span', Yii::t('rms', 'No'), ['class' => 'label label-danger']);
                    }
                    // return $model->approved ? Yii::t('rms', 'Yes') : Yii::t('rms', 'No');
                }
            ],
            [
                'attribute' => 'created_at',
                'format' => ['date', 'php:dMY'],
            ],
            [
                'attribute' => 'updated_at',
                'format' => ['date', 'php:dMY'],
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
</script>