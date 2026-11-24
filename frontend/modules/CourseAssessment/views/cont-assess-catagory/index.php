<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\CourseAssessment\models\ContAssessCatagorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Course Assessment Catagories');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cont-assess-catagory-index">

    <h3><?= Html::encode($this->title) ?>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Create Course Assessment Catagory'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
    </p>
    </h3>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute' => 'cat_acadYear_relId',
                'value' => function($model){
                    return $model->catAcadYearRel->sch_year_desc;
                },
                'filter' => ArrayHelper::map(\backend\models\SchoolYear::find()->all(),'id','sch_year_desc'),
            ],[
                'attribute' => 'cat_sem_relId',
                'value' => function($model){
                    return $model->catSemRel->semester_name;
                },
                'filter' => ArrayHelper::map(\backend\models\Semester::find()->all(),'id','semester_name'),
            ],[
                'attribute' => 'cat_coff_relId',
                'format' => 'raw',
                'value'=>function($model){
                    $model = $model->catCoffRel;
                    return $model->coff2detCoff2Rel->coff2GroupRel->getGsDisp()."-{$model->coff2detCoff2Rel->coff2GroupRel->gsGroupRel->group_name}";
                },
                'filter' => ArrayHelper::map(\backend\models\Department::find()->all(),'id','dept_name'),
            ],
            [
                'attribute' => 'cat_course_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $model->catCourseRel->getCourseCredits();
                }
            ], [
                'attribute' => 'cat_catagory_relId',
                'format' => 'raw',
                'value' => function($model){
                    return $model->catCatagoryRel->catagory_name;
                }
            ],
            
            'cat_maxPoint',
            //'cat_created_at',
            //'cat_created_by',
            //'cat_updated_by',
            //'cat_updated_at',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
