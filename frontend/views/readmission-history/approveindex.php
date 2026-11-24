<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/* @var $searchModel frontend\models\ReadmissionHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Approve Re-admission';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="readmission-history-index">

    <h1><?php // Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //echo Html::a('Request Readmission', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            // 'rh_student_relId',
            // 'rh_academicCal_relId',
            // 'rh_semester_reiId',
            // 'rh_request',
            //'rh_status',
                [
                    'attribute'=>'rh_status',
                    'header'=>'Request Status',
                    //'filter' => ['1'=>'Approved', '0'=>'Pending'],
                    'format'=>'raw',    
                    'value' => function($model, $key, $index)
                    {   
                       // if($model->expiryDate=='2018-01-09')
                        if($model->rh_status>=1)
                        {
                            return '<button class="btn btn-success"> Accepted</button>';
                        }
                      //  else if($model->expiryDate-date('d')<=29 && $model->expiryDate-date('d')>10)
                        else if($model->rh_status<0)
                        {   
                            return '<button class="btn btn-danger">Rjected</button>';
                        }
                        else{
                            return '<button class="btn btn-warning">Waiting for Approval</button>';
                        }
                    },
              ],

            //'rh_updatedBy',
            //'rh_updatedAt',
            //'rh_createdAt',
            //'rh_createdBy',

            //['class' => 'yii\grid\ActionColumn'],
               [
                'class' => 'yii\grid\ActionColumn', 
                //'template' => '{view} {update}',
                'template' => '{update}',
                //  'template' => '{view} ',
                  'buttons' => [
                    // 'view' => function($url, $model, $key) {
                    //     return Html::a('<i class="fa glyphicon glyphicon-eye-open"></i>', ['approve-view','id'=>$model->id]); 
                    //     // return Html::a('<i class="fa glyphicon glyphicon-eye-open"></i>', ['value' => Url::to('index.php?r=patient/create'), 'id' => 'modalButton3']);
                    // },     
                    'update' => function($url, $model, $key) {
                        return Html::a('<i class="fa glyphicon glyphicon-plus"></i>',['approve-readmission', 'id'=>$model->id],[ 'data-toggle'=>'tooltip', 'class' => 'btn btn-success','title'=>'Approve Readmission']); 
                        }
                    // 'update' => function($url, $model, $key) {
                    //     return Html::a('<i class="fa glyphicon glyphicon-pencil"></i>',['/patient/update', 'id'=>$model->id, 'institutionId'=>$model->institutionId], [ 'target'=> '_blank', 'data-toggle'=>'tooltip', 'title'=>'update']); 
                    // }
                ]
            ],
        ],
    ]); ?>
</div>
