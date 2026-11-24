<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel frontend\modules\ClassSchedule\models\CsAvailableClassRoomSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Available Class Rooms');
$this->params['breadcrumbs'][] = ['label' => Yii::t('rms', 'Class Schedule'), 'url' => ['/ClassSchedule/']];
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="cs-available-class-room-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Register Class Room'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'room',
            'num_of_seats',
            [
                'attribute' => 'projector',
                'value' => function($model){
                    return $model->projector == 1? 'Available':'Not Available';
                },
                'filter' => ArrayHelper::map(frontend\modules\ClassSchedule\models\CsAvailableClassRoom::find()->groupBy('projector')->all(),'projector', function($model){ return $model->projector == 1? 'Available':'Not Available'; }),
            ],[
                'attribute' => 'projector_board',
                'value' => function($model){
                    return $model->projector_board == 1? 'Available':'Not Available';
                },
                'filter' => ArrayHelper::map(frontend\modules\ClassSchedule\models\CsAvailableClassRoom::find()->groupBy('projector_board')->all(),'projector_board', function($model){ return $model->projector_board == 1? 'Available':'Not Available'; }),
            ],[
                'attribute' => 'white_board',
                'value' => function($model){
                    return $model->white_board == 1? 'Available':'Not Available';
                },
                'filter' => ArrayHelper::map(frontend\modules\ClassSchedule\models\CsAvailableClassRoom::find()->groupBy('white_board')->all(),'white_board', function($model){ return $model->white_board == 1? 'Available':'Not Available'; }),
            ],[
                'attribute' => 'black_board',
                'value' => function($model){
                    return $model->black_board == 1? 'Available':'Not Available';
                },
                'filter' => ArrayHelper::map(frontend\modules\ClassSchedule\models\CsAvailableClassRoom::find()->groupBy('black_board')->all(),'black_board', function($model){ return $model->black_board == 1? 'Available':'Not Available'; }),
            ],

            'description:ntext',
            
            [
                'attribute' => 'status',
                'value' => function($model){
                    return $model->status == 1? 'Available':'Not Available';
                },
                'filter' => ArrayHelper::map(frontend\modules\ClassSchedule\models\CsAvailableClassRoom::find()->groupBy('status')->all(),'status', function($model){ return $model->status == 1? 'Available':'Not Available'; }),
            ],[
                'attribute' => 'created_by',
                'value' => function($model){
                    return $model->createdBy->username;
                }
            ],
            //'updated_by',
            //'updated_at',
            
            [
                'attribute' => 'created_at',
                'value' => function($model){
                    return date("d/m/Y",$model->created_at);
                }
            ],
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
