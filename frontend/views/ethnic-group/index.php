<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\EthnicGroupSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Ethnic Groups');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="ethnic-group-index">

    <h3><?= Html::encode($this->title) ?>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Register Ethnic Group'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
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
                'attribute' => 'region_relId',
                'filter' => ArrayHelper::map(\backend\models\Region::find()->all(),'id','region_name'),
                'value' => function($model){
                    return $model->regionRel->region_name;
                }
            ],
            'name',
            // 'remark',
            [
                'attribute' => 'created_by',
                'value' => function($model){
                    return $model->createdBy ? $model->createdBy->username : '-';
                }
            ],
            
            [
                'attribute' => 'created_at',
                'value' => function($model){
                    return date("d/m/Y",$model->created_at);
                }
            ],
            //'created_at',
            //'updated_at',
            //'updated_by',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
