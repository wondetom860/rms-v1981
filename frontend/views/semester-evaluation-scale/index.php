<?php

use yii\helpers\Html;
use yii\grid\GridView;
use yii\widgets\Pjax;
/** @var yii\web\View $this */
/* @var $searchModel backend\models\SemesterEvaluationScaleSearch */
/* @var $dataProvider yii\data\ActiveDataProvider */

$this->title = Yii::t('rms', 'Semester Evaluation Scales');
$this->params['breadcrumbs'][] = $this->title;
?>
<div class="semester-evaluation-scale-index">

    <h3><?= Html::encode($this->title) ?></h3>
    <?php // echo $this->render('_search', ['model' => $searchModel]); ?>

    <p style="text-align:right;">
        <?= Html::a(Yii::t('rms', 'Create Semester Evaluation Scale'), ['create'], ['class' => 'btn btn-success btn-xs']) ?>
    </p>
<?php Pjax::begin(); ?>    <?= GridView::widget([
        'dataProvider' => $dataProvider,
        'filterModel' => $searchModel,
        'columns' => [
            ['class' => 'yii\grid\SerialColumn'],

            'ses_name',
            [
                'attribute' => 'ses_maxPoint',
                'value' => function($model){ return is_null($model->ses_maxPoint) ? '-' : sprintf("%0.2f",$model->ses_maxPoint); }
            ],[
                'attribute' => 'ses_minPoint',
                'value' => function($model){ return is_null($model->ses_minPoint) ? '-' : sprintf("%0.2f",$model->ses_minPoint); }
            ],
            
            'ses_pass_status',
            'remark:ntext',

            ['class' => 'yii\grid\ActionColumn'],
        ],
    ]); ?>
<?php Pjax::end(); ?></div>
