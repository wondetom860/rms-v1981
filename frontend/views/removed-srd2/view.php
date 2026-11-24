<?php

use yii\helpers\Html;
use yii\widgets\DetailView;

/* @var $this yii\web\View */
/* @var $model backend\models\RemovedSrd2 */

$course = $model->coff2detRel->coff2detCourseRel;
$this->title = "[ {$course->getCourseCredits()} ] :" . $course->course_title;
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Removed Enrols'), 'url' => ['index']];
$this->params['breadcrumbs'][] = $this->title;
\yii\web\YiiAsset::register($this);
?>
<div class="removed-srd2-view">

    <h3><?= Html::encode($this->title) ?>
        <?php if ($model->hod_approved != 1 && $model->reg_approved != 1) { ?>
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
        <?php } ?>
    </h3>

    <?= DetailView::widget([
        'model' => $model,
        'attributes' => [
            [
                'attribute' => 'std_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return Html::a($model->stdRel->getFullName(), ['student/view', 'id' => $model->std_relId]);
                }
            ],
            [
                'attribute' => 'semreg_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->semregRel->getSemregHeader() . " >> " . $model->semregRel->semregClslvlRel->getClslvlDisp();
                }
            ],
            [
                'attribute' => 'coff2det_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->coff2detRel->coff2detCoff2Rel->getCourseOfferingHeader() . " >> " . $model->coff2detRel->coff2detCourseRel->course_title;
                }
            ],
            'total_mark',
            [
                'attribute' => 'grade_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->gradeRel ? $model->gradeRel->gs_ltr_grade : '-';
                }
            ],
            [
                'attribute' => 'Course Department',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->coff2detRel->coff2detCourseRel->courseDeptRel->dept_name;
                }
            ],
            [
                'attribute' => 'hod_approved',
                'format' => 'raw',
                'value' => function ($model) {
                    $btnApprove = '';
                    if (Yii::$app->user->can('DepartmentHead') && $model->reg_approved != 1) {
                        if ($model->hod_approved != 1) {
                            $btnApprove = Html::a('Approve Removal', ['hod-approve', 'id' => $model->id], [
                                'class' => 'btn btn-warning btn-xs pull-right',
                                'data' => [
                                    'confirm' => 'Are you sure you want to approve this removal request?',
                                    'method' => 'post',
                                ],
                            ]);
                        } else {
                            $btnApprove = Html::a('Revert', ['hod-approve', 'id' => $model->id], [
                                'class' => 'btn btn-danger btn-xs pull-right',
                                'data' => [
                                    'confirm' => 'Are you sure you want to revert approval?',
                                    'method' => 'post',
                                ],
                            ]);
                        }
                    }
                    $text = $model->hod_approved == 1 ? 'Yes' : 'No';

                    return $text . $btnApprove;
                }
            ],
            [
                'attribute' => 'reg_approved',
                'format' => 'raw',
                'value' => function ($model) {
                    $btnApprove = '';
                    if (Yii::$app->user->can('Registrar') && $model->hod_approved == 1) {
                        if ($model->reg_approved != 1) {
                            $btnApprove = Html::a('Approve Removal', ['reg-approve', 'id' => $model->id], [
                                'class' => 'btn btn-danger btn-xs pull-right',
                                'data' => [
                                    'confirm' => 'Are you sure you want to approve this removal request?',
                                    'method' => 'post',
                                ],
                            ]);
                        } else {
                            $btnApprove = Html::a('Revert Removal', ['reg-approve', 'id' => $model->id], [
                                'class' => 'btn btn-warning btn-xs pull-right',
                                'data' => [
                                    'confirm' => 'Are you sure you want to revert request?',
                                    'method' => 'post',
                                ],
                            ]);
                        }
                    }
                    $text = $model->reg_approved == 1 ? 'Yes' : 'No';

                    return $text . $btnApprove;
                }
            ],
            [
                'attribute' => 'created_at',
                'format' => 'raw',
                'value' => function ($model) {
                    return date('dMY', $model->created_at);
                }
            ],
            [
                'attribute' => 'created_by',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->createdBy->getFullName();
                }
            ],
            [
                'attribute' => 'updated_at',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->updated_at ? date('dMY', $model->updated_at) : '';
                }
            ],
            [
                'attribute' => 'updated_by',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->updated_by ? $model->updatedBy->getFullName() : '';
                }
            ],
            // 'updated_at',
            // 'updated_by',
            // 'srd2_serialized:ntext',
            // 'cont_ass_marks:ntext',
            'data:ntext',
        ],
    ]) ?>

</div>