<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\FacultyCourseAssignmentSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Faculty Course Assignments');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="faculty-course-assignment-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p style="text-align: right;">
        <?php
        if (Yii::$app->user->can('DepartmentHead')) {
            // echo Html::a(Yii::t('rms', 'Import existing'), ['create'], ['class' => 'btn btn-success xs']);
            // echo Html::a(Yii::t('rms', 'Generate report'), ['create'], ['class' => 'btn btn-warning btn-xs','onclick' => 'generateReport();']);
            // echo Html::a(Yii::t('rms', '#'), ['create'], ['class' => 'btn btn-success xs']);
        }

        ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'academicYear',
                'value' => function ($model) {
                    return $model->coff2detRel->coff2detAcProgRel->getAcadYearHeader();
                },
                'filter' => ArrayHelper::map(\frontend\modules\AcademicCalendar\models\AcAcademicYear::find()->orderBy(['school_year' => SORT_DESC, 'semester' => SORT_DESC])->all(), 'id', function ($model) {
                    return $model->getAcHeader();
                }),
            ],
            [
                'attribute' => 'coff2det_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    $gs = $model->coff2detRel->coff2detCoff2Rel->coff2GroupRel;
                    return $model->coff2detRel->coff2detCoff2Rel->coff2GroupRel->getGsDisp() . "({$gs->gsGroupRel->group_name})";
                },
                // 'filter' => ArrayHelper::map(\backend\models\CourseOffering2Detail::find()->innerJoin('course_offering2','course_offering2.id=course_offering2_detail.coff2det_coff2_relId')->orderBy(['coff2_group_relId' => SORT_DESC])->all(),'id',function($model){
                //         $gs = $model->coff2detCoff2Rel->coff2GroupRel;
                //         return "{$gs->gsDeptRel->dept_code}-[{$gs->gsClslvlRel->clslvl_num}-{$gs->gsSemRel->semester_name}].({$gs->gsGroupRel->group_name})"; 
                //     }),
            ],
            [
                'attribute' => 'course',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->coff2detRel->coff2detCourseRel->getCourseCredits();
                }
            ],
            [
                'attribute' => 'inst_relId',
                'value' => function ($model) {
                    return $model->instRel->getFullName();
                }
            ],
            'assign_as:ntext',
            'remark',

            [
                'class' => 'yii\grid\ActionColumn',
                'template' => "{view}{update}",
                'visible' => Yii::$app->user->can('Admin') || Yii::$app->user->can('DepartmentHead') || Yii::$app->user->can('Registrar') || Yii::$app->user->can('AcademicDean'),
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>