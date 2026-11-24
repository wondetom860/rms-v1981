<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;

?>
<div class="cont-assess-catagory-index">
        <?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'cat_course_relId',
                'value'=>'catCourseRel.course_title',
            ],
            [
                'attribute'=>'cat_name_relId',
                'value'=>'catNameRel.cont_cat_name',
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
            

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?>
</div>