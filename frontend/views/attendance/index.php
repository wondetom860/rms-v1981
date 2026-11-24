<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
// use kartik\grid\GridView;
// use backend\models\AttendancedetailSearch;

/** @var yii\web\View $this */
/* @var $searchModel frontend\models\AttendanceSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Class Attendance';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="attendance-index">
    <p >
        <?= Html::a(' Collect Attendance ', ['create-attendance'], ['class' => 'btn btn-success pull-right']) ?>
        <?= Html::a(' Attendance Form ', ['class-attendance-form'], ['class' => 'btn btn-primary pull-right']) ?>  
        <?= Html::a(' Exam Attendance Sheet ', ['exam-attendance-sheet'], ['class' => 'btn btn-warning pull-right']) ?> 
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           [
                'attribute'=>'acadYear',
                'value'=>function($model){
                    return $model->aGroupRel->gsAcadYearRel->sch_year_desc."-".$model->aGroupRel->gsSemRel->semester_name;
                },
                // 'filter' => ArrayHelper::map(\backend\models\Attendance::find()->innerJoin('w_group_setup','w_group_setup.id = attendance.a_group_relId')->all(),function($model){ return $model->aGroupRel->gs_acadYear_relId; },function($model){ 
                //         return $model->aGroupRel->gsAcadYearRel->sch_year_desc;
                //     })
            ],
            // [
            //     'attribute'=>'semester',
            //     'value'=>function($model){
            //         return $model->aGroupRel->gsSemRel->semester_name;
            //     },
            //     'filter' => ArrayHelper::map(\backend\models\Attendance::find()->innerJoin('w_group_setup','w_group_setup.id = attendance.a_group_relId')->all(),function($model){ return $model->aGroupRel->gs_sem_relId; },function($model){ 
            //             return $model->aGroupRel->gsSemRel->semester_name;
            //         })
            // ],
            [
                'attribute'=>'course',
                'value'=>function($model){
                    return $model->aCourseRel->course_title;
                },
                // 'filter' => ArrayHelper::map(\backend\models\Attendance::find()->all(),'a_course_relId',function($model){ 
                //     return $model->aCourseRel->course_title; 
                //     // ." ({$model->aCourseRel->course_title} - {$model->aCoffRel->getOfferingAcadYearInfo()})"
                // })
            ],
            
            [
                'attribute'=>'a_group_relId',
                'label'=>'Group',
                'value'=>function($model){
                    return $model->aGroupRel->gsGroupRel->group_name." {$model->course}";
                },
                // 'filter' => ArrayHelper::map(\backend\models\Attendance::find()->all(),'a_group_relId',function($model){ 
                //         return $model->aGroupRel->gsGroupRel->group_name; 
                //         // ." ({$model->aCourseRel->course_title} - {$model->aCoffRel->getOfferingAcadYearInfo()})"
                //     })
            ],
            'a_hour',
            [
                'attribute' => 'presents',
                'value' => function($model){ 
                    return count(json_decode($model->a_presentStudentsList,true)).' Student(s) present'; 
                }
            ],
            [
                'attribute'=>'a_createdAt',
                'value'=>function($model){
                    return date_format(date_create($model->a_createdAt),'d M, Y');
                },
            ],
            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => [],
                'header'=>'Actions',
                'template' => '{view} {update} {delete}',
                'visibleButtons'=>[
                    'view'=> function($model, $key, $index){
                          return $model->a_createdBy == Yii::$app->user->id;
                     },
                     'delete'=> function($model, $key, $index){
                          return $model->a_createdBy == Yii::$app->user->id;
                     },
                     'update'=> function($model, $key, $index){
                          return $model->a_createdBy == Yii::$app->user->id;
                     },
                ]
            ],
        ],
    ]); ?>
</div>




