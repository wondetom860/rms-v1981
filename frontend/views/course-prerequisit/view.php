<?php

use yii\helpers\ArrayHelper;
use yii\helpers\Html;
use yii\widgets\DetailView;

/** @var yii\web\View $this */
/* @var $model backend\models\CoursePrerequisit */

$this->title = $model->preqMainCourseRel->course_title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Course Prerequisits'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-prerequisit-view">

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
                'attribute' => 'preq_adm_relId',
                'filter' => ArrayHelper::map(\backend\models\Admission::find()->all(), 'id', 'admission_name'),
                'value' => function ($model) {
                    return is_null($model->preq_adm_relId) ? 'All' : $model->preqAdmRel->admission_name;
                }
            ],
            [
                'attribute' => 'preq_prog_relId',
                'filter' => ArrayHelper::map(\backend\models\Program::find()->all(), 'id', 'program_name'),
                'value' => function ($model) {
                    return is_null($model->preq_prog_relId) ? 'All' : $model->preqProgRel->program_name;
                }
            ],

            [
                'attribute' => 'preqMainCourseRel.course_title',
                'value' => function ($model) {
                    return $model->preqMainCourseRel->course_title . "[{$model->preqMainCourseRel->course_code}]";
                },
            ],
            [
                'attribute' => 'preqPreqCourseRel.course_title',
                'value' => function ($model) {
                    return is_null($model->preq_preq_course_relId) ? 'NONE' : $model->preqPreqCourseRel->course_title . '(' . $model->preqPreqCourseRel->course_code . ')';
                },
            ],

            [
                'attribute' => 'preq_dept_relId',
                'value' => function ($model) {
                    return is_null($model->preq_dept_relId) ? 'All' : $model->preqDeptRel->dept_name;
                },
            ],
            [
                'attribute' => 'preq_spec_relId',
                'value' => function ($model) {
                    return is_null($model->preq_spec_relId) ? 'All' : $model->preqSpecRel->spec_name;
                }
            ],
            [
                'attribute' => 'preq_created_by',
                'value' => function($model){
                    return $model->preqCreatedBy->username;
                }
            ],
            [
                'attribute' => 'preq_created_at',
                'value' => function($model){
                    return date_format(date_create($model->preq_created_at),'dMY');
                }
            ],
            [
                'attribute' => 'preq_updated_by',
                'value' => function($model){
                    return $model->preqUpdatedBy ? $model->preqUpdatedBy->username : '';
                }
            ],
            [
                'attribute' => 'preq_updated_at',
                'value' => function($model){
                    return $model->preq_updated_at ? date_format(date_create($model->preq_created_at),'dMY') : '';
                }
            ],
            'remark:ntext',
        ],
    ]) ?>

</div>
