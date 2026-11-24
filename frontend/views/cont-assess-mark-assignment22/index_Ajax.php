<?php
use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
?>
<div class="cont-assess-mark-assignment-index">
<?php Pjax::begin(); ?>    <?=
    GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'cat_coff_relId',
                'value'=>function($model){
                    $courseId = \backend\models\CourseOffering2Detail::findOne($model->cat_coff_relId)->coff2det_course_relId;
                    return \backend\models\Course::findOne($courseId)->course_title;
                }
            ],
            [
                'attribute'=>'ma_catagory_relId',
                'value'=>function($model){
                    $CatagoryId = \backend\models\ContAssessCatagory2::findOne($model->ma_catagory_relId)->cat_name_relId;
                    return \backend\models\ContAssessCatagoryName::findOne($CatagoryId)->cont_cat_name;
                }
            ],
            'ma_instance',
            'ma_possiblePoint',
             [
                'attribute'=>'ma_created_by',
                'value'=>'maCreatedBy.first_name',
            ],
            [
                'attribute'=>'ma_created_at',
                'value'=> function($model){
                    return date_format(date_create($model->ma_created_at),'D, M d, Y');
                } ,
            ],
            
            // 'ma_updated_by',
            // 'ma_updated_at',
            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]);
    ?>
<?php Pjax::end(); ?>
</div>