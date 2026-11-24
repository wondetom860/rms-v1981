<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\modules\SemesterEvaluation\models\WEvaluationPointEntryTableSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Evaluation Point Catagory Tables');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wevaluation-point-entry-table-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('rms', 'Create Evaluation Point Catagory Table'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => [
            'style'=>'overflow: auto; word-wrap: break-word; font-size:10pt;',
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'epet_eet_relId',
                'value'=> function($model){
                    return $model->epetEetRel->eetEtRel->et_name .' ['.$model->epetEetRel->eet_evaluator_role.' => '.$model->epetEetRel->eet_reviewee_role.']';
                },
                'filter' => ArrayHelper::map(backend\modules\SemesterEvaluation\models\WEvaluationPointEntryTable::find()->groupBy('epet_eet_relId')->all(),'epet_eet_relId',function($model){ return $model->epetEetRel->eetEtRel->et_name; }),
            ],
            [
                'attribute'=>'epet_ep_relId',
                'value'=>function($model){
                    return $model->epetEpRel->ep_desc;
                }
            ],
            'epet_max_point',
            [
                'attribute'=>'Check',
                'value'=> function($model){
                    return $model->checkAltered($model) ? 'Valid':'Invalid';
                } 
            ],

            [
                'class' => 'yii\grid\ActionColumn',
                'visible' => Yii::$app->user->can('Admin'),
            ],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
