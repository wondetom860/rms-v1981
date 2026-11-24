<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/* @var $searchModel frontend\models\ReadmissionHistorySearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Re-admission Request ';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="readmission-history-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a('Request Readmission', ['create'], ['class' => 'btn btn-success']) ?>
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
                            return '<button class="btn btn-success">√</button>';
                        }
                      //  else if($model->expiryDate-date('d')<=29 && $model->expiryDate-date('d')>10)
                        else if($model->rh_status<0)
                        {   
                            return '<button class="btn btn-danger">x</button>';
                        }
                        else{
                            return '<button class="btn btn-warning">?</button>';
                        }
                    },
              ],

            //'rh_updatedBy',
            //'rh_updatedAt',
            //'rh_createdAt',
            //'rh_createdBy',

            //['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
