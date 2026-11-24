<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\modules\SemesterEvaluation\models\WEvaluationPointsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Evaluation Points');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wevaluation-points-index table-responsive">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('rms', 'Add Evaluation Points'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => [
                'style'=>'overflow: auto; word-wrap: break-word;',
            ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ep_desc:ntext',
            [
                'attribute'=>'ep_ceated_at',
                'value'=>function($model){
                    return date_format(date_create($model->ep_ceated_at),'d M, Y');
                }
            ],
            [
                'attribute'=>'ep_created_by',
                'value'=>'epCreatedBy.username',
            ],
            // 'ep_updated_at',
            // 'ep_updated_by',
            [
                'attribute'=>'Check',
                'value'=> function($model){
                    return $model->checkAltered($model) ? 'Valid':'Invalid';
                } 
            ],

            ['class' => 'yii\grid\ActionColumn','header'=>'Options',],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
