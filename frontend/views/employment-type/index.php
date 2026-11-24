<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\EmploymentTypeSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Employment Types');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="employment-type-index">

    <h3><?= Html::encode($this->title) ?></h3>

    <p class="pull-right">
        <?= Html::a(Yii::t('rms', 'Create New Employment Type'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
    </p>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'et_name',
            'cont_renewal_interval_month',
            [
                'attribute' => 'et_created_at',
                'value' => function($model){
                    return date("d/m/Y",$model->et_created_at);
                }
            ],
            [
                'attribute' => 'et_created_by',
                'value' => function($model){
                    return $model->etCreatedBy?$model->etCreatedBy->username:'-';
                }
            ],
            
            //'et_updated_at',
            //'et_updated_by',
            //'remarks:ntext',
            //'data:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>
