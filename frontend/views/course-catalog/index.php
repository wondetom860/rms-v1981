<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;

/** @var yii\web\View $this */
/* @var $searchModel backend\models\CourseCatalogSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Course Catalogs');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-catalog-index table-responsive">

    <h3><?= Html::encode($this->title) ?>
        <?php // echo $this->render('_search', ['model' => $searchModel]); 
        ?>

        <p class="pull-right">
            <?= Yii::$app->user->can('Admin') ? Html::a(Yii::t('rms', 'Create New Course Catalog'), ['create'], ['class' => 'btn btn-success btn-xs']) : '' ?>
        </p>
    </h3>

    <?php Pjax::begin(); ?>
    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'cocatCourseRel.course_code',
                'value' => function ($model) {
                    $model->determineCategory();
                    return $model->cocatCourseRel->course_code . '[ ' . $model->cocatCourseRel->course_crdthr . ' ]';
                },
            ],
            [
                'attribute' => 'cocatCourseRel.course_title',
                'value' => 'cocatCourseRel.course_title',
            ],

            [
                'attribute' => 'cocatAdmissionRel.admission_name',
                'value' => 'cocatAdmissionRel.admission_name',
            ],

            [
                'attribute' => 'cocatProgramRel.program_name',
                'value' => 'cocatProgramRel.program_name',
            ],
            [
                'attribute' => 'cocatDeptRel.dept_name',
                'value' => function ($model) {
                    return is_null($model->cocat_dept_relId) ? 'For all Depts' : $model->cocatDeptRel->dept_code;
                },
                'filter' => \yii\helpers\ArrayHelper::map(\backend\models\CourseCatalog::find()->groupBy('cocat_dept_relId')->all(), function ($model) {
                    return is_null($model->cocat_dept_relId) ? 'For all Depts' : $model->cocatDeptRel->dept_name;
                }, function ($model) {
                    return is_null($model->cocat_dept_relId) ? 'For all Depts' : $model->cocatDeptRel->dept_code;
                })
            ],
            [
                'attribute' => 'cocatClslvlRel.clslvl_num',
                'format' => 'raw',
                'value' => function ($model) {
                    if (is_null($model->cocat_clslvl_relId)) {
                        return 'All';
                    }
                    return $model->cocatClslvlRel->getClslvlDisp();
                },
            ],
            [
                'attribute' => 'cocatSpecRel.spec_name',
                'value' => function ($model) {
                    return is_null($model->cocat_spec_relId) ? 'Common for dept. specs' : $model->cocatSpecRel->spec_name;
                }
            ],
            [
                'attribute' => 'cocatSemRel.semester_name',
                'value' => 'cocatSemRel.semester_name',
            ],
            [
                'attribute' => 'course_category_relId',
                'value' => function ($model) {
                    if ($model->course_category_relId) {
                        if ((strtoupper($model->courseCategoryRel->catgeory_name) == 'MAJOR') && ($model->cocat_courseMajor != 1)) {
                            $model->cocat_courseMajor = 1;
                            $model->save();
                            $model->refresh();
                        }else if((strtoupper($model->courseCategoryRel->catgeory_name) != 'MAJOR') && ($model->cocat_courseMajor == 1)){
                            $model->cocat_courseMajor = 0;
                            $model->save();
                            $model->refresh();
                        }
                        return $model->courseCategoryRel->catgeory_name;
                    }
                    return NULL;
                },
                'filter' => \yii\helpers\ArrayHelper::map(\backend\models\CourseCatalog::find()->groupBy('course_category_relId')->all(), function ($model) {
                    return $model->course_category_relId;
                }, function ($model) {
                    return $model->course_category_relId ? $model->courseCategoryRel->catgeory_name : NULL;
                })
            ],
            // 
            [
                'attribute' => 'cocat_status',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->cocat_status == 1 ?
                        "<span class='btn-success btn-xs' title='Can be offered'>Active</span>" :
                        "<span class='btn-warning btn-xs' title='Can not offerable'>Not</span>";
                },
                'filter' => \yii\helpers\ArrayHelper::map(\backend\models\CourseCatalog::find()->groupBy('cocat_status')->all(), function ($model) {
                    return $model->cocat_status;
                }, function ($model) {
                    return $model->cocat_status ? 'Active' : 'Not Active';
                })
            ],

            [
                'attribute' => 'cocat_courseProject',
                'format' => 'raw',
                'header' => 'Project',
                'value' => function ($model) {
                    return is_null($model->cocat_courseProject) ? "<span class='btn-danger btn-xs' title='{$model->cocat_courseProject}'>Uknown</span>" : ($model->cocat_courseProject == 1 ?
                        "<span class='btn-success btn-xs' title='Project Course'>Project</span>" :
                        "<span class='btn-warning btn-xs' title='Non Project'>Not</span>");
                },
            ],

            [
                'attribute' => 'cocat_created_by',
                'value' => 'cocatCreatedBy.username',
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => [],
                'header' => 'Actions',
                'template' => '{view} {update} {delete}',
                'visibleButtons' => [
                    'view' => function ($model, $key, $index) {
                        return Yii::$app->user->can('Admin') || Yii::$app->user->can('Registrar');
                    },
                    'update' => function ($model, $key, $index) {
                        return Yii::$app->user->can('Admin');
                    },
                    'delete' => function ($model, $key, $index) {
                        return Yii::$app->user->can('Admin');
                    },
                ]
            ],
        ],
    ]); ?>
    <?php Pjax::end(); ?>
</div>