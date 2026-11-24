<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\ScholarshipTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Sponsorship Types');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="scholarship-type-index">

    <h3><?= Html::encode($this->title) ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Add New Sponsorship Type'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
    </p>
    </h3>
    <div class="container-fluid table-responsive">
   
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => [
                'style'=>'overflow: auto; word-wrap: break-word;',
            ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'schType_name',
            [
                'attribute'=>'schType_pay',
                'value'=>function($model){
                    return $model->schType_pay == 1?'NO':'Free';
                }
            ],
            'schType_desc',
            'schType_by',
            [
                'attribute'=>'schType_created_by',
                'value'=>'schTypeCreatedBy.username',
            ],[
                'attribute'=>'schType_created_at',
                'value'=> function($model){
                    return date_format( date_create($model->schType_created_at) ,"d/m/Y");
                },
            ],
            
            // 'schType_updated_at',
            // 'schType_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?>     
    </div></div>
