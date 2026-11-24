<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
use yii\widgets\ActiveForm;

?>
<div class="container-fluid table-responsive">
        <?php Pjax::begin(); ?>    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            // 'filterModel' => $searchModel,
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                [
                    'attribute'=>'ima_course',
                    'value'=>'imaCatagorySetupRel.catCourseRel.course_title', 
                ],
                // [
                //     'attribute'=>'ima_catagorySetup_relId',
                //     'value'=>function($model){
                //         return md5($model->ima_catagorySetup_relId);
                //     }
                // ],
                'ima_instanceName',
                'ima_possiblePoint',
                [
                    'attribute'=>'ima_created_by',
                    'value'=>'imaCreatedBy.first_name',
                ],
                // 'ima_created_at',
                // 'ima_updated_by',
                // 'ima_updated_at',

                [
                    'class' => 'yii\grid\ActionColumn',
                    'contentOptions' => [],
                    'header'=>'Actions',
                    'template' => '{view} {update} {delete}',
                    'visibleButtons'=>[
                        'view'=> false,
                         'update'=> function($model, $key, $index){
                              return (is_null($model->ima_approved) || $model->ima_approved == 0) && $model->ima_created_by == Yii::$app->user->identity->id;
                         },
                         'delete'=> function($model, $key, $index){
                              return (is_null($model->ima_approved) || $model->ima_approved == 0)  && $model->ima_created_by == Yii::$app->user->identity->id;
                         },
                    ],
                ],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
    </div>