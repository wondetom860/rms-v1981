<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\modules\SemesterEvaluation\models\WEvaluationEntryTableSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('decris', 'Evaluation Control Entry Tables');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="wevaluation-entry-table-index">

    <h2><?= Html::encode($this->title) ?></h2>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p>
        <?= Html::a(Yii::t('decris', 'Create Evaluation Entry Table'), ['create'], ['class' => 'btn btn-success']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'eet_et_relId',
                'value'=>'eetEtRel.et_name',
            ],
            'eet_evaluator_role',
            'eet_reviewee_role',
            [
                'attribute'=>'eet_max_point',
                'value'=>function($model){
                    return sprintf("%0.2f",$model->eet_max_point);
                }
            ],
            // 'eet_created_by',
            // 'eet_created_at',
            // 'eet_updated_at',
            // 'eet_updated_by',
            [
                'attribute'=>'Valid?',
                'value'=>function($model){
                    return $model->checkAltered($model) ? 'Yes':'No';
                }
            ],

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
