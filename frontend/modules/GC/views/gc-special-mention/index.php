<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/* @var $this yii\web\View */
/* @var $searchModel frontend\modules\GC\models\GcSpecialMentionSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Special Mentions');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="gc-special-mention-index">

    <h3><?= Html::encode($this->title) ?>

        <p class="pull-right">
            <?= Html::a(Yii::t('rms', 'Create Special Mention'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
        </p>
    </h3>

    <?php Pjax::begin(); ?>
    <?php // echo $this->render('_search', ['model' => $searchModel]); 
    ?>

    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'gsm_name',
            [
                'attribute' => 'gsm_minCGPA',
                'value' => function($model){
                    return sprintf('%0.2f',$model->gsm_minCGPA);
                }
            ],
            [
                'attribute' => 'gsm_maxCGPA',
                'value' => function($model){
                    return sprintf('%0.2f',$model->gsm_maxCGPA);
                }
            ],
            [
                'attribute' => 'gsm_created_by',
                'value' => function($model){
                    return $model->gsmCreatedBy->username;
                }
            ],
            //'gsm_created_at',
            //'gsm_updated_at',
            //'gsm_updated_by',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>

    <?php Pjax::end(); ?>

</div>