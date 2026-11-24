<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/* @var $searchModel frontend\models\StudentWithdrawalHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Approve Withdrawal ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="student-withdrawal-history-index">

    <h1><?php // Html::encode($this->title) ?></h1>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?php //echo Html::a('Create Withdrawal', ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            //'id',
            'swh_student_relId',
            // [
            // 'attribute'=>'swh_student_relId',
            // 'label'=>'Student Name',
            // 'value'=>function($model){
            //     return $model->swhUpdatedBy->firstName;
            // }
            // ],
            'swh_academicyear_relId',
            'swh_semester_relId',
            //'swh_withdrawalreason_relId',
            [
            'attribute'=>'swh_withdrawalreason_relId',
            'label'=>'Reason',
            'value'=>function($model){
                return $model->swhWithdrawalreasonRel->withdrawalres_description;
            }
            ],
            //'swh_request_status',
            //'swh_comment:ntext',
            // 'swh_createdBy',
            // 'swh_updatedBy',
            // 'swh_createdAt',
            // 'swh_updatedAt',
             [
                    'attribute'=>'swh_request_status',
                    'header'=>'Status',
                    //'filter' => ['1'=>'Approved', '0'=>'Pending'],
                    'format'=>'raw',    
                    'value' => function($model, $key, $index)
                    {   
                       // if($model->expiryDate=='2018-01-09')
                        if($model->swh_request_status>=1)
                        {
                            return '<button class="btn btn-success">√</button>';
                        }
                      //  else if($model->expiryDate-date('d')<=29 && $model->expiryDate-date('d')>10)
                        else if($model->swh_request_status<0)
                        {   
                            return '<button class="btn btn-danger">x</button>';
                        }
                        else{
                            return '<button class="btn btn-warning">?</button>';
                        }
                    },
              ],
           // ['class' => 'yii\grid\ActionColumn'],
                // Action

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
                        return Html::a('<i class="fa glyphicon glyphicon-plus"></i>',['approve-update', 'id'=>$model->id],[ 'data-toggle'=>'tooltip', 'class' => 'btn btn-success','title'=>'Go To Approval']); 
                        }
                    // 'update' => function($url, $model, $key) {
                    //     return Html::a('<i class="fa glyphicon glyphicon-pencil"></i>',['/patient/update', 'id'=>$model->id, 'institutionId'=>$model->institutionId], [ 'target'=> '_blank', 'data-toggle'=>'tooltip', 'title'=>'update']); 
                    // }
                ]
            ],

                // Action
        ],
    ]); ?>
</div>
