<?php

use yii\helpers\Html;
use yii\helpers\ArrayHelper;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\SemesterEvaluationPointsSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Semester Evaluation Points');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-evaluation-points-index table-responsive">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p style="text-align: right;">
        <?= Html::a(Yii::t('rms', 'Add New Semester Evaluation Points'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'options' => [
            'style'=>'overflow: auto; word-wrap: break-word;'
        ],
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            [
                'attribute'=>'semevPoint_semev_relId',
                'value'=>'semevPointSemevRel.semev_type_desc',
                'filter' => ArrayHelper::map(\backend\models\SemesterEvaluationTypes::find()->all(),'id','semev_type_desc'),
            ],
            'sem_eval_point_desc:ntext',
            'remark:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
