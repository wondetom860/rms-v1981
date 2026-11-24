<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\modules\SemesterEvaluation\models\WEvaluationTypesSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Evaluation Types');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wevaluation-types-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('rms', 'Create Evaluation Types'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>

    <div class="container-fluid table-responsive">
        <?php Pjax::begin(); ?>    <?= GridView::widget([
            'dataProvider' => $dataProvider,
            'filterModel' => $searchModel,
            'options' => [
                'style'=>'overflow: auto; word-wrap: break-word; font-size:10pt;',
            ],
            'columns' => [
                ['class' => 'yii\grid\SerialColumn'],

                'et_name',
                'et_description:ntext',
                [
                    'attribute'=>'et_per_course',
                    'value'=>function($model){
                        return $model->et_per_course == 1 ? 'Evaluation is per course' : 'Not Per Course';
                    }
                ],
                [
                    'attribute'=>'Check',
                    'value'=> function($model){
                        return $model->checkAltered($model) ? 'Valid':'Invalid';
                    } 
                ],

                [
                    'class' => 'yii\grid\ActionColumn',
                    'header'=>'Options',
                    'options'=>[
                        'style'=>'overflow: auto; word-wrap: text-nowrap;',
                    ]
                ],
            ],
        ]); ?>
    <?php Pjax::end(); ?>
    </div>
</div>
