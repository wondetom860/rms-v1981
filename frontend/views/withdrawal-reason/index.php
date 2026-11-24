<?php

use yii\helpers\Html;
use yii\grid\GridView;

/** @var yii\web\View $this */
/* @var $searchModel frontend\models\WithdrawalReasonSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = 'Withdrawal Reason';
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="withdrawal-reason-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p class="pull-right">
        <?= Html::a('Create Withdrawal Reason', ['create'], ['class' => 'btn btn-success btn-xs']) ?>
    </p>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

           // 'id',
            'withdrawalres_type',
            'withdrawalres_description',
            [
                'attribute' => 'remove_course_enrolls',
                'value' => function($model){
                    return $model->remove_course_enrolls ? "Yes" : "No";
                }
            ],[
                'attribute' => 'readmittable',
                'value' => function($model){
                    return $model->readmittable ? "Yes" : "No";
                }
            ],
            
            // 'withdrawalres_updatedAt',
            // 'withdrawalres_createdBy_relId',
            // 'withdrawalres_createdAt',
            //'withdrawalres_updatedby_relId',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
</div>
