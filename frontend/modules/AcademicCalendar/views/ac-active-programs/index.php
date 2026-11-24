<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\AcademicCalendar\models\AcActiveProgramsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

// $modelN = new frontend\modules\AcademicCalendar\models\AcActivePrograms();

$this->title = Yii::t('rms', 'Programs');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Academic Calendar'), 'url' => ['/AcademicCalendar/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ac-active-programs-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="hidden">
        <?= Html::a(Yii::t('rms', 'Add Active Program'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => [
                'style'=>'overflow: auto; word-wrap: break-word;',
            ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'academic_year_relId',
                'value' => function($model){
                    return $model->academicYearRel->getAcHeader();
                },
                'filter' => ArrayHelper::map(\frontend\modules\AcademicCalendar\models\AcActivePrograms::find()
                        ->innerJoin('ac_academic_year','ac_academic_year.id=ac_active_programs.academic_year_relId')
                        ->orderBy(['school_year' => SORT_DESC,'semester' => SORT_DESC])
                        ->groupBy('academic_year_relId')->all(),'academic_year_relId',
                        function($model){ return $model->academicYearRel->getAcHeader(); })
            ],
            [
                'attribute' => 'program_relId',
                'value' => function($model){
                    return $model->admissionRel->admission_name."-".$model->programRel->program_name;
                },
                'filter' => ArrayHelper::map(\frontend\modules\AcademicCalendar\models\AcActivePrograms::find()->groupBy('program_relId')->all(),'program_relId',
                        function($model){ return $model->programRel->program_name; })
            ],
            
            [
                'attribute' => 'dept_relId',
                'value' => function($model){
                    return $model->deptRel->dept_name;
                },
                'filter' => ArrayHelper::map(\frontend\modules\AcademicCalendar\models\AcActivePrograms::find()->groupBy('dept_relId')->all(),'dept_relId',
                        function($model){ return $model->deptRel->dept_name; })
            ],[
                'attribute' => 'class_level_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $model->classLevelRel->getClslvlDisp();
                }
            ],[
                'attribute' => 'semester_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $model->semesterRel? $model->semesterRel->semester_name : '';
                }
            ],[
                'attribute' => 'spec_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $model->specRel ? $model->specRel->spec_name : '';
                }
            ],[
                'attribute' => 'graduate_class',
                'value' => function($model){ return $model->graduate_class? 'GC' : 'Non-GC'; } ,
                'filter' => ArrayHelper::map(\frontend\modules\AcademicCalendar\models\AcActivePrograms::find()->groupBy('graduate_class')->all(),'graduate_class',
                        function($model){ return $model->graduate_class ? 'Graduate class':'Non-graduate class'; })
            ],[
                'attribute' => 'on_internship',
                'value' => function($model){ return $model->on_internship? 'Intern' : 'Non-Intern'; } ,
                'filter' => ArrayHelper::map(\frontend\modules\AcademicCalendar\models\AcActivePrograms::find()->groupBy('on_internship')->all(),'on_internship',
                        function($model){ return $model->on_internship ? 'Internship':'Non-Internship'; })
            ],[
                'attribute' => 'status',
                'format' => 'raw',
                'value' => function($model){
                    return $model->getStatus();
                },
                'filter' => ArrayHelper::map(\frontend\modules\AcademicCalendar\models\AcActivePrograms::find()->groupBy('status')->all(),'status',
                        function($model){ return $model->stss[$model->status]; })
            ],
            //'created_at',
            //'created_by',
            //'updated_at',
            //'updated_by',
            //'data:ntext',
            //'remark:ntext',

            [
                'class' => 'yii\grid\ActionColumn',
            ],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
