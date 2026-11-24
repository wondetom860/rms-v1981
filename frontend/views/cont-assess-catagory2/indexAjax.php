<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

?>
<div class="cont-assess-catagory-index">
        <?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        // 'filterModel' => $searchModel,
        // 'rowOption'=>
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'cat_course_relId',
                'value'=>'catCourseRel.course_title',
            ],
            [
                'attribute'=>'cat_catagory_relId',
                'value'=>'catCatagoryRel.catagory_name',
            ],
            'cat_maxPoint',
            [
                'attribute'=>'cat_created_by',
                'value'=>'catCreatedBy.first_name',
            ],
            [
                'attribute'=>'cat_created_at',
                'value'=> function($model){
                    return date_format(date_create($model->cat_created_at),'D, M d, Y');
                } ,
            ],
            

            [
                'class' => 'yii\grid\ActionColumn',
                'contentOptions' => [],
                'header'=>'Action',
                'template' => '{view} {update} {delete}',
                'visibleButtons'=>[
                    'view'=> false,
                     'update'=> function($model, $key, $index){
                        // course cordiantor can modify the assessment breakdown.
                        // any instructor assigned for the course can manage it. 

                        // provided that the course asseessment is NOT done one.
                          return $model->cat_created_by == Yii::$app->user->identity->id;
                     },
                     'delete'=> function($model, $key, $index){
                          return $model->cat_created_by == Yii::$app->user->identity->id;
                     },
                ]
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?>
</div>