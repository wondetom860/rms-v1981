<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\helpers\ArrayHelper;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\CourseOfferingTempSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Course Offerings');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="course-offering-temp-index">

    <h3><?= Html::encode($this->title) ?>
        <p class="pull-right hidden">
            <?= Html::a(Yii::t('rms', 'Create Course Offering Temp'), ['create'], ['class' => 'btn btn-success']) ?>
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
                'attribute' => 'cot_acadYear_relId',
                'value' => function ($model) {
                    return $model->cotAcadYearRel->sch_year_desc;
                },
                'filter' => ArrayHelper::map(
                    \backend\models\CourseOfferingTemp::find()
                        ->groupBy('cot_acadYear_relId')->all(),
                    'cot_acadYear_relId',
                    function ($model) {
                        return $model->cotAcadYearRel->sch_year_desc;
                    }
                ),
            ],
            [
                'attribute' => 'cot_sem_relId',
                'value' => function ($model) {
                    return $model->cotSemRel->semester_name;
                },
                'filter' => ArrayHelper::map(\backend\models\CourseOfferingTemp::find()
                    ->groupBy('cot_sem_relId')->all(), 'cot_sem_relId', function ($model) {
                    return $model->cotSemRel->semester_name;
                })
            ],
            [
                'attribute' => 'cot_adm_relId',
                'value' => function ($model) {
                    return $model->cotAdmRel->admission_name;
                },
                'filter' => ArrayHelper::map(
                    \backend\models\CourseOfferingTemp::find()
                        ->groupBy('cot_adm_relId')->all(),
                    'cot_adm_relId',
                    function ($model) {
                        return $model->cotAdmRel->admission_name;
                    }
                )
            ],
            [
                'attribute' => 'cot_prog_relId',
                'value' => function ($model) {
                    return $model->cotProgRel->program_name;
                },
                'filter' => ArrayHelper::map(
                    \backend\models\CourseOfferingTemp::find()
                        ->groupBy('cot_prog_relId')->all(),
                    'cot_prog_relId',
                    function ($model) {
                        return $model->cotProgRel->program_name;
                    }
                )
            ],
            [
                'attribute' => 'cot_dept_relId',
                'value' => function ($model) {
                    return $model->cotDeptRel->dept_code;
                },
                'filter' => ArrayHelper::map(
                    \backend\models\CourseOfferingTemp::find()
                        ->groupBy('cot_dept_relId')->all(),
                    'cot_dept_relId',
                    function ($model) {
                        return $model->cotDeptRel->dept_name;
                    }
                )
            ],
            [
                'attribute' => 'cot_clslvel_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    return $model->cotClslvelRel->getClslvlDisp();
                },
                'filter' => ArrayHelper::map(
                    \backend\models\CourseOfferingTemp::find()
                        ->groupBy('cot_clslvel_relId')->all(),
                    'cot_clslvel_relId',
                    function ($model) {
                        return $model->cotClslvelRel->clslvl_name;
                    }
                )
            ],
            [
                'attribute' => 'cot_spec_relId',
                'value' => function ($model) {
                    return $model->cot_spec_relId ? $model->cotSpecRel->spec_name : "-";
                }
            ],
            [
                'attribute' => 'cot_cocat_IDS',
                'format' => 'raw',
                'value' => function ($model) {
                    return count(json_decode($model->cot_cocat_IDS, true));
                    // return $this->render('__report/courses-offered',['model' => $model]);
                }
            ],
            //'cot_cocat_IDS:ntext',
            //'cot_created_at',
            //'cot_created_by',
            //'cot_updated_at',
            //'cot_updated_by',
            //'cot_locked',
            //'cot_registrarApproved',
            //'cot_registrarApproved_at',
            //'cot_registrarApproved_by',
            [
                'attribute' => 'cot_acProg_relId',
                'format' => 'raw',
                'value' => function ($model) {
                    $header = '';
                    // $model->linkAcProg();
                    if ($model->cotAcProgRel !== null) {
                        // var_dump($model->cot_acProg_relId);
                        $header = $model->cotAcProgRel->getClassLevelHeader();
                    } else {
                        $mn = $model->linkAcProg();
                        if (is_bool($mn) && $mn) {
                            $header = $model->cotAcProgRel->getClassLevelHeader();
                        } else {
                            $header = $mn;
                        }
                    }

                    return "<a href='/AcademicCalendar/ac-active-programs/view?id={$model->cot_acProg_relId}'>{$header}</a>";
                    // return $model->cotAcProgRel ? $model->cotAcProgRel->getClassLevelHeader() : NULL;
                },
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>